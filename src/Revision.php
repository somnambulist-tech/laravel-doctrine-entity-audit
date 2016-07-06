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

/**
 * Class Revision
 *
 * Revision is returned from {@link AuditReader::getRevisions()}
 *
 * @package    Somnambulist\EntityAudit
 * @subpackage Somnambulist\EntityAudit\Revision
 */
class Revision
{

    /**
     * @var int
     */
    private $rev;

    /**
     * @var \DateTimeInterface
     */
    private $timestamp;

    /**
     * @var string
     */
    private $username;

    /**
     * Constructor.
     *
     * @param int                $rev
     * @param \DateTimeInterface $timestamp
     * @param string             $username
     */
    function __construct($rev, $timestamp, $username)
    {
        $this->rev       = $rev;
        $this->timestamp = $timestamp;
        $this->username  = $username;
    }

    /**
     * @return int
     */
    public function getRev()
    {
        return $this->rev;
    }

    /**
     * @return \DateTimeInterface
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }
}
