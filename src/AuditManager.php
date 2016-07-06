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

use Somnambulist\EntityAudit\Metadata\MetadataFactory;

/**
 * Class AuditManager
 *
 * Audit Manager grants access to metadata and configuration and has a factory method for audit queries.
 *
 * @package    Somnambulist\EntityAudit
 * @subpackage Somnambulist\EntityAudit\AuditManager
 */
class AuditManager
{

    /**
     * @var AuditConfiguration
     */
    private $config;

    /**
     * @var Metadata\MetadataFactory
     */
    private $metadataFactory;

    /**
     * @var AuditReader
     */
    private $reader;



    /**
     * Constructor.
     *
     * @param AuditConfiguration $config
     * @param MetadataFactory    $factory
     * @param AuditReader        $reader
     */
    public function __construct(AuditConfiguration $config, MetadataFactory $factory, AuditReader $reader)
    {
        $this->config          = $config;
        $this->metadataFactory = $factory;
        $this->reader          = $reader;
    }

    /**
     * @return Metadata\MetadataFactory
     */
    public function getMetadataFactory()
    {
        return $this->metadataFactory;
    }

    /**
     * @return AuditConfiguration
     */
    public function getConfiguration()
    {
        return $this->config;
    }

    /**
     * @return AuditReader
     */
    public function getAuditReader()
    {
        return $this->reader;
    }
}
