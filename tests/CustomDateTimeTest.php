<?php

namespace Somnambulist\EntityAudit\Tests;

use Doctrine\DBAL\Logging\SQLLogger;
use Doctrine\DBAL\Types;
use Somnambulist\EntityAudit\Tests\Fixtures\Core\Cat;
use Somnambulist\EntityAudit\Tests\Fixtures\Core\Fox;
use Somnambulist\EntityAudit\Tests\Fixtures\Core\UserAudit;
use Somnambulist\EntityAudit\Tests\Stubs\MyDateTime;
use Somnambulist\EntityAudit\Tests\Stubs\Types\CustomDateTimeType;
use function count;
use function get_class;
use function is_object;
use function strpos;

/**
 * Class CustomDateTimeTest
 *
 * @package    Somnambulist\EntityAudit\Tests
 * @subpackage Somnambulist\EntityAudit\Tests\CustomDateTimeTest
 */
class CustomDateTimeTest extends BaseTest
{
    protected $schemaEntities = [
        'Somnambulist\EntityAudit\Tests\Fixtures\Core\ArticleAudit',
        'Somnambulist\EntityAudit\Tests\Fixtures\Core\UserAudit',
        'Somnambulist\EntityAudit\Tests\Fixtures\Core\AnimalAudit',
        'Somnambulist\EntityAudit\Tests\Fixtures\Core\Fox',
        'Somnambulist\EntityAudit\Tests\Fixtures\Core\Rabbit',
        'Somnambulist\EntityAudit\Tests\Fixtures\Core\PetAudit',
        'Somnambulist\EntityAudit\Tests\Fixtures\Core\Cat',
        'Somnambulist\EntityAudit\Tests\Fixtures\Core\Dog',
    ];

    protected $auditedEntities = [
        'Somnambulist\EntityAudit\Tests\Fixtures\Core\ArticleAudit',
        'Somnambulist\EntityAudit\Tests\Fixtures\Core\UserAudit',
        'Somnambulist\EntityAudit\Tests\Fixtures\Core\AnimalAudit',
        'Somnambulist\EntityAudit\Tests\Fixtures\Core\Rabbit',
        'Somnambulist\EntityAudit\Tests\Fixtures\Core\Fox',
        'Somnambulist\EntityAudit\Tests\Fixtures\Core\Cat',
        'Somnambulist\EntityAudit\Tests\Fixtures\Core\Dog',
    ];

    public function setUp(): void
    {
        Types\Type::getTypeRegistry()->override('datetime', new CustomDateTimeType);

        parent::setUp();
    }

    /**
     * @group cur
     */
    public function testFind()
    {
        $user = new UserAudit("beberlei");
        $foxy = new Fox('foxy', 55);
        $cat  = new Cat('pusheen', '#b5a89f');

        /*
         * Admittedly this is not a great test, but it is one way to determine if the LogRevisionsListener
         * will use the Type registry to make a new DateTime instance when the revision is made. If it worked
         * the datetime instance bound should be the custom type set by the type. Basically it's more a case
         * of can a "datetime" type translate "now" to the custom datetime class instance or not.
         */
        $this->em->getConfiguration()->setSQLLogger(new class implements SQLLogger {
            private $hasCustomDateTime = false;
            public function startQuery($sql, ?array $params = null, ?array $types = null)
            {
                if (false !== strpos($sql, 'INSERT INTO revisions')) {
                    foreach ($params as $param) {
                        if (is_object($param) && $param instanceof MyDateTime) {
                            $this->hasCustomDateTime = true;
                        }
                    }
                }
            }

            public function stopQuery()
            {
            }

            public function hasCustomDateTime(): bool
            {
                return $this->hasCustomDateTime;
            }
        });

        $this->em->persist($cat);
        $this->em->persist($user);
        $this->em->persist($foxy);
        $this->em->flush();

        $this->assertTrue($this->em->getConfiguration()->getSQLLogger()->hasCustomDateTime());
        $this->assertEquals(1, count($this->em->getConnection()->fetchAll('SELECT id FROM revisions')));
    }
}
