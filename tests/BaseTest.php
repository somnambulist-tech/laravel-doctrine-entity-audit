<?php
/*
 * (c) 2011 SimpleThings GmbH
 *
 * @package SimpleThings\EntityAudit
 * @author Benjamin Eberlei <eberlei@simplethings.de>
 * @link http://www.simplethings.de
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
 */

namespace Somnambulist\EntityAudit\Tests;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\DBAL\Logging\EchoSQLLogger;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Doctrine\ORM\Tools\SchemaTool;
use Illuminate\Contracts\Auth\Guard;
use PHPUnit\Framework\TestCase;
use Somnambulist\EntityAudit\AuditConfiguration;
use Somnambulist\EntityAudit\AuditManager;
use Somnambulist\EntityAudit\AuditReader;
use Somnambulist\EntityAudit\EventListener\CreateSchemaListener;
use Somnambulist\EntityAudit\EventListener\LogRevisionsListener;
use Somnambulist\EntityAudit\Metadata\MetadataFactory;
use Somnambulist\EntityAudit\Support\TableConfiguration;
use Somnambulist\EntityAudit\UserResolver;

/**
 * Class BaseTest
 *
 * @package    Somnambulist\EntityAudit\Tests
 * @subpackage Somnambulist\EntityAudit\Tests\BaseTest
 */
abstract class BaseTest extends TestCase
{
    /**
     * @var EntityManager
     */
    protected $em = null;

    /**
     * @var AuditManager
     */
    protected $auditManager = null;

    /**
     * @var array
     */
    protected $schemaEntities = [];

    /**
     * @var array
     */
    protected $auditedEntities = [];

    public function setUp(): void
    {
        $reader = new AnnotationReader();
        $driver = new AnnotationDriver($reader);
        $driver->addPaths([__DIR__ . '/Fixtures']);
        $config = new Configuration();
        $config->setProxyDir(sys_get_temp_dir());
        $config->setProxyNamespace('Somnambulist\EntityAudit\Tests\Proxies');
        $config->setMetadataDriverImpl($driver);

        $conn = [
            'driver'   => $GLOBALS['DOCTRINE_DRIVER'],
            'memory'   => $GLOBALS['DOCTRINE_MEMORY'],
            'dbname'   => $GLOBALS['DOCTRINE_DATABASE'],
            'user'     => $GLOBALS['DOCTRINE_USER'],
            'password' => $GLOBALS['DOCTRINE_PASSWORD'],
            'host'     => $GLOBALS['DOCTRINE_HOST'],
        ];

        if (isset($GLOBALS['DOCTRINE_PATH'])) {
            $conn['path'] = $GLOBALS['DOCTRINE_PATH'];
        }

        if (php_sapi_name() == 'cli'
            && isset($_SERVER['argv'])
            && (in_array('-v', $_SERVER['argv']) || in_array('--verbose', $_SERVER['argv']))
        ) {
            $config->setSQLLogger(new EchoSQLLogger());
        }

        $this->em = EntityManager::create($conn, $config);

        $guard = $this->getMockBuilder(Guard::class)
            ->onlyMethods(['user', 'check', 'guest', 'id', 'validate', 'setUser'])
            ->getMock()
        ;
        $guard
            ->method('user')
            ->willReturn($this->returnValue(null))
        ;

        $auditMeta   = new MetadataFactory($this->auditedEntities);
        $auditConfig = new AuditConfiguration(
            new UserResolver($guard, 'beberlei', 'beberlei'),
            new TableConfiguration([
                'table_suffix' => '_audit',
                'table_prefix' => '',
            ]),
            ['ignoreme']
        );
        $auditReader = new AuditReader($this->em, $auditConfig, $auditMeta);

        $this->auditManager = new AuditManager($auditConfig, $auditMeta, $auditReader);

        $schemaTool = new SchemaTool($this->em);
        $em         = $this->em;

        $em->getEventManager()->addEventSubscriber(new CreateSchemaListener($this->auditManager));
        $em->getEventManager()->addEventSubscriber(new LogRevisionsListener($this->auditManager));

        try {
            $schemaTool->createSchema(array_map(function ($value) use ($em) {
                return $em->getClassMetadata($value);
            }, $this->schemaEntities));
        } catch (\Exception $e) {
            if ($GLOBALS['DOCTRINE_DRIVER'] != 'pdo_mysql' ||
                !($e instanceof \PDOException && strpos($e->getMessage(), 'Base table or view already exists') !== false)
            ) {
                throw $e;
            }
        }
    }

    public function tearDown(): void
    {
        $schemaTool = new SchemaTool($this->em);
        $em         = $this->em;

        try {
            $schemaTool->dropDatabase();
        } catch (\Exception $e) {
            if ($GLOBALS['DOCTRINE_DRIVER'] != 'pdo_mysql' ||
                !($e instanceof \PDOException && strpos($e->getMessage(), 'Base table or view already exists') !== false)
            ) {
                throw $e;
            }
        }
    }
}
