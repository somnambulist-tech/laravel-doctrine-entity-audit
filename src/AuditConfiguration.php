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
use Somnambulist\EntityAudit\Support\TableConfiguration;

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
     * @var UserResolverInterface
     */
    private $userResolver;

    /**
     * @var TableConfiguration
     */
    private $tableConfig;

    /**
     * @var array
     */
    private $globalIgnoreColumns = [];



    /**
     * Constructor.
     *
     * @param UserResolverInterface $userResolver
     * @param TableConfiguration    $table
     * @param array                 $ignoreColumns
     */
    public function __construct(
        UserResolverInterface $userResolver,
        TableConfiguration $table,
        $ignoreColumns
    )
    {
        $this->userResolver        = $userResolver;
        $this->tableConfig         = $table;
        $this->globalIgnoreColumns = $ignoreColumns;
    }

    public function setUserResolver(UserResolverInterface $userResolver)
    {
        $this->userResolver = $userResolver;

        return $this;
    }

    /**
     * @return string
     */
    public function getCurrentUsername()
    {
        return $this->userResolver->resolve();
    }

    /**
     * @return array
     */
    public function getGlobalIgnoreColumns()
    {
        return $this->globalIgnoreColumns;
    }

    /**
     * @param ClassMetadataInfo $metadata
     *
     * @return string
     */
    public function getTableName(ClassMetadataInfo $metadata)
    {
        return $this->tableConfig->getTableName($metadata);
    }

    /**
     * @return TableConfiguration
     */
    public function getTableConfig()
    {
        return $this->tableConfig;
    }

    /**
     * @return string
     */
    public function getTablePrefix()
    {
        return $this->tableConfig->getTablePrefix();
    }

    /**
     * @return string
     */
    public function getTableSuffix()
    {
        return $this->tableConfig->getTableSuffix();
    }

    /**
     * @return string
     */
    public function getRevisionFieldName()
    {
        return $this->tableConfig->getRevisionFieldName();
    }

    /**
     * @return string
     */
    public function getRevisionTypeFieldName()
    {
        return $this->tableConfig->getRevisionTypeFieldName();
    }

    /**
     * @return string
     */
    public function getRevisionIdFieldType()
    {
        return $this->tableConfig->getRevisionIdFieldType();
    }

    /**
     * @return string
     */
    public function getRevisionTableName()
    {
        return $this->tableConfig->getRevisionTableName();
    }
}
