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

namespace Somnambulist\EntityAudit;

use Doctrine\ORM\Mapping\ClassMetadataInfo;

/**
 * Class AuditConfiguration
 *
 * @package    Somnambulist\EntityAudit
 * @subpackage Somnambulist\EntityAudit\AuditConfiguration
 * @author     Dave Redfern
 */
class AuditConfiguration
{

    /**
     * @var UserResolver
     */
    private $userResolver;

    /**
     * @var string
     */
    private $prefix                = '';

    /**
     * @var string
     */
    private $suffix                = '_audit';

    /**
     * @var string
     */
    private $revisionFieldName     = 'rev';

    /**
     * @var string
     */
    private $revisionTypeFieldName = 'revtype';

    /**
     * @var string
     */
    private $revisionTableName    = 'revisions';

    /**
     * @var array
     */
    private $auditedEntityClasses = [];

    /**
     * @var array
     */
    private $globalIgnoreColumns = [];

    /**
     * @var string
     */
    private $revisionIdFieldType = 'integer';



    /**
     * Constructor.
     *
     * @param UserResolver $userResolver
     */
    public function __construct(UserResolver $userResolver)
    {
        $this->userResolver = $userResolver;
    }

    /**
     * @param ClassMetadataInfo $metadata
     *
     * @return string
     */
    public function getTableName(ClassMetadataInfo $metadata)
    {
        $tableName = $metadata->getTableName();

        //## Fix for doctrine/orm >= 2.5
        if (method_exists($metadata, 'getSchemaName') && $metadata->getSchemaName()) {
            $tableName = $metadata->getSchemaName() . '.' . $tableName;
        }

        return $this->getTablePrefix() . $tableName . $this->getTableSuffix();
    }

    /**
     * @return string
     */
    public function getTablePrefix()
    {
        return $this->prefix;
    }

    /**
     * @param string $prefix
     *
     * @return $this
     */
    public function setTablePrefix($prefix)
    {
        $this->prefix = $prefix;

        return $this;
    }

    /**
     * @return string
     */
    public function getTableSuffix()
    {
        return $this->suffix;
    }

    /**
     * @param string $suffix
     *
     * @return $this
     */
    public function setTableSuffix($suffix)
    {
        $this->suffix = $suffix;

        return $this;
    }

    /**
     * @return string
     */
    public function getRevisionFieldName()
    {
        return $this->revisionFieldName;
    }

    /**
     * @param string $revisionFieldName
     *
     * @return $this
     */
    public function setRevisionFieldName($revisionFieldName)
    {
        $this->revisionFieldName = $revisionFieldName;

        return $this;
    }

    /**
     * @return string
     */
    public function getRevisionTypeFieldName()
    {
        return $this->revisionTypeFieldName;
    }

    /**
     * @param string $revisionTypeFieldName
     *
     * @return $this
     */
    public function setRevisionTypeFieldName($revisionTypeFieldName)
    {
        $this->revisionTypeFieldName = $revisionTypeFieldName;

        return $this;
    }

    /**
     * @return string
     */
    public function getRevisionTableName()
    {
        return $this->revisionTableName;
    }

    /**
     * @param string $revisionTableName
     *
     * @return $this
     */
    public function setRevisionTableName($revisionTableName)
    {
        $this->revisionTableName = $revisionTableName;

        return $this;
    }

    /**
     * @return array
     */
    public function getAuditedEntityClasses()
    {
        return $this->auditedEntityClasses;
    }

    /**
     * @param array $classes
     *
     * @return $this
     */
    public function setAuditedEntityClasses(array $classes)
    {
        $this->auditedEntityClasses = $classes;

        return $this;
    }

    /**
     * @return array
     */
    public function getGlobalIgnoreColumns()
    {
        return $this->globalIgnoreColumns;
    }

    /**
     * @param array $columns
     *
     * @return $this
     */
    public function setGlobalIgnoreColumns(array $columns)
    {
        $this->globalIgnoreColumns = $columns;

        return $this;
    }

    /**
     * @return string
     */
    public function getRevisionIdFieldType()
    {
        return $this->revisionIdFieldType;
    }

    /**
     * @param string $revisionIdFieldType
     *
     * @return $this
     */
    public function setRevisionIdFieldType($revisionIdFieldType)
    {
        $this->revisionIdFieldType = $revisionIdFieldType;

        return $this;
    }

    /**
     * @return Metadata\MetadataFactory
     */
    public function createMetadataFactory()
    {
        return new Metadata\MetadataFactory($this->auditedEntityClasses);
    }

    /**
     * @return string
     */
    public function getCurrentUsername()
    {
        return $this->userResolver->resolve();
    }
}
