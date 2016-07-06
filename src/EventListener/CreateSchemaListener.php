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

namespace Somnambulist\EntityAudit\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\DBAL\Schema\Column;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\ORM\Tools\Event\GenerateSchemaEventArgs;
use Doctrine\ORM\Tools\Event\GenerateSchemaTableEventArgs;
use Doctrine\ORM\Tools\ToolEvents;
use Somnambulist\EntityAudit\AuditManager;

/**
 * Class CreateSchemaListener
 *
 * @package    Somnambulist\EntityAudit\EventListener
 * @subpackage Somnambulist\EntityAudit\EventListener\CreateSchemaListener
 */
class CreateSchemaListener implements EventSubscriber
{
    /**
     * @var \Somnambulist\EntityAudit\AuditConfiguration
     */
    private $config;

    /**
     * @var \Somnambulist\EntityAudit\Metadata\MetadataFactory
     */
    private $metadataFactory;

    /**
     * Constructor.
     *
     * @param AuditManager $auditManager
     */
    public function __construct(AuditManager $auditManager)
    {
        $this->config          = $auditManager->getConfiguration();
        $this->metadataFactory = $auditManager->getMetadataFactory();
    }

    /**
     * @return array
     */
    public function getSubscribedEvents()
    {
        return [
            ToolEvents::postGenerateSchemaTable,
            ToolEvents::postGenerateSchema,
        ];
    }

    /**
     * @param GenerateSchemaTableEventArgs $eventArgs
     *
     * @throws \Exception
     */
    public function postGenerateSchemaTable(GenerateSchemaTableEventArgs $eventArgs)
    {
        $cm = $eventArgs->getClassMetadata();

        if (!$this->metadataFactory->isAudited($cm->name)) {
            $audited = false;
            if ($cm->isInheritanceTypeJoined() && $cm->rootEntityName == $cm->name) {
                foreach ($cm->subClasses as $subClass) {
                    if ($this->metadataFactory->isAudited($subClass)) {
                        $audited = true;
                    }
                }
            }
            if (!$audited) {
                return;
            }
        }

        $schema        = $eventArgs->getSchema();
        $entityTable   = $eventArgs->getClassTable();
        $revisionTable = $schema->createTable(
            $this->config->getTablePrefix() . $entityTable->getName() . $this->config->getTableSuffix()
        );

        foreach ($entityTable->getColumns() as $column) {
            /* @var Column $column */
            $revisionTable->addColumn($column->getName(), $column->getType()->getName(), array_merge(
                $column->toArray(),
                ['notnull' => false, 'autoincrement' => false]
            ));
        }

        $revisionTable->addColumn($this->config->getRevisionFieldName(), $this->config->getRevisionIdFieldType());
        $revisionTable->addColumn($this->config->getRevisionTypeFieldName(), 'string', ['length' => 4]);

        if (!in_array($cm->inheritanceType, [
            ClassMetadataInfo::INHERITANCE_TYPE_NONE, ClassMetadataInfo::INHERITANCE_TYPE_JOINED,
            ClassMetadataInfo::INHERITANCE_TYPE_SINGLE_TABLE,
        ])) {
            throw new \Exception(sprintf('Inheritance type "%s" is not yet supported', $cm->inheritanceType));
        }

        $pkColumns   = $entityTable->getPrimaryKey()->getColumns();
        $pkColumns[] = $this->config->getRevisionFieldName();
        $revisionTable->setPrimaryKey($pkColumns);
        $revIndexName = $this->config->getRevisionFieldName() . '_' . md5($revisionTable->getName()) . '_idx';
        $revisionTable->addIndex([$this->config->getRevisionFieldName()], $revIndexName);
    }

    /**
     * @param GenerateSchemaEventArgs $eventArgs
     */
    public function postGenerateSchema(GenerateSchemaEventArgs $eventArgs)
    {
        $schema         = $eventArgs->getSchema();
        $revisionsTable = $schema->createTable($this->config->getRevisionTableName());
        $revisionsTable->addColumn('id', $this->config->getRevisionIdFieldType(), [
            'autoincrement' => true,
        ]);
        $revisionsTable->addColumn('timestamp', 'datetime');
        $revisionsTable->addColumn('username', 'string');
        $revisionsTable->setPrimaryKey(['id']);
    }
}
