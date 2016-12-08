<?php
/*
 * (c) 2011 SimpleThings GmbH
 *
 * @package SimpleThings\EntityAudit
 * @author Benjamin Eberlei <eberlei@simplethings.de>
 * @author Andrew Tch <andrew.tchircoff@gmail.com>
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

namespace Somnambulist\EntityAudit\Collection;

use Closure;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Somnambulist\EntityAudit\AuditConfiguration;
use Somnambulist\EntityAudit\AuditReader;
use Somnambulist\EntityAudit\Exception\AuditedCollectionException;

/**
 * Class AuditedCollection
 *
 * @package    Somnambulist\EntityAudit\Collection
 * @subpackage Somnambulist\EntityAudit\Collection\AuditedCollection
 */
class AuditedCollection implements Collection
{
    /**
     * Related audit reader instance
     *
     * @var AuditReader
     */
    protected $auditReader;

    /**
     * Class to fetch
     * @var string
     */
    protected $class;

    /**
     * Foreign keys for target entity
     * @var array
     */
    protected $foreignKeys;

    /**
     * Maximum revision to fetch
     * @var string
     */
    protected $revision;

    /**
     * @var AuditConfiguration
     */
    protected $configuration;

    /**
     * @var ClassMetadataInfo
     */
    protected $metadata;

    /**
     * Entity array. If can be:
     * - empty, if the collection has not been initialized yet
     * - store entity
     * - contain audited entity
     *
     * @var array
     */
    protected $entities = [];

    /**
     * Definition of current association
     *
     * @var array
     */
    protected $associationDefinition = [];

    /**
     * @var bool
     */
    protected $initialized = false;

    /**
     * Constructor.
     *
     * @param AuditReader       $auditReader
     * @param string            $class
     * @param ClassMetadataInfo $classMeta
     * @param array             $associationDefinition
     * @param array             $foreignKeys
     * @param string            $revision
     */
    public function __construct(AuditReader $auditReader, $class, ClassMetadataInfo $classMeta, array $associationDefinition, array $foreignKeys, $revision)
    {
        $this->auditReader           = $auditReader;
        $this->class                 = $class;
        $this->foreignKeys           = $foreignKeys;
        $this->revision              = $revision;
        $this->configuration         = $auditReader->getConfiguration();
        $this->metadata              = $classMeta;
        $this->associationDefinition = $associationDefinition;
    }

    /**
     * {@inheritdoc}
     */
    public function add($element)
    {
        throw AuditedCollectionException::collectionIsReadOnly();
    }

    /**
     * {@inheritdoc}
     */
    public function clear()
    {
        $this->entities    = [];
        $this->initialized = false;
    }

    /**
     * {@inheritdoc}
     */
    public function contains($element)
    {
        $this->forceLoad();

        return (bool)array_search($element, $this->entities);
    }

    /**
     * {@inheritdoc}
     */
    public function isEmpty()
    {
        $this->initialize();

        return count($this->entities) == 0;
    }

    /**
     * {@inheritdoc}
     */
    public function remove($key)
    {
        throw AuditedCollectionException::collectionIsReadOnly();
    }

    /**
     * {@inheritdoc}
     */
    public function removeElement($element)
    {
        throw AuditedCollectionException::collectionIsReadOnly();
    }

    /**
     * {@inheritdoc}
     */
    public function containsKey($key)
    {
        $this->initialize();

        return array_key_exists($key, $this->entities);
    }

    /**
     * {@inheritdoc}
     */
    public function get($key)
    {
        return $this->offsetGet($key);
    }

    /**
     * {@inheritdoc}
     */
    public function getKeys()
    {
        $this->initialize();

        return array_keys($this->entities);
    }

    /**
     * {@inheritdoc}
     */
    public function getValues()
    {
        $this->forceLoad();

        return array_values($this->entities);
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $value)
    {
        throw AuditedCollectionException::collectionIsReadOnly();
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        $this->forceLoad();

        return $this->entities;
    }

    /**
     * {@inheritdoc}
     */
    public function first()
    {
        $this->forceLoad();

        return reset($this->entities);
    }

    /**
     * {@inheritdoc}
     */
    public function last()
    {
        $this->forceLoad();

        return end($this->entities);
    }

    /**
     * {@inheritdoc}
     */
    public function key()
    {
        $this->forceLoad();

        return key($this->entities);
    }

    /**
     * {@inheritdoc}
     */
    public function current()
    {
        $this->forceLoad();

        return current($this->entities);
    }

    /**
     * {@inheritdoc}
     */
    public function next()
    {
        $this->forceLoad();

        return next($this->entities);
    }

    /**
     * {@inheritdoc}
     */
    public function exists(Closure $p)
    {
        $this->forceLoad();

        foreach ($this->entities as $entity) {
            if ($p($entity)) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function filter(Closure $p)
    {
        $this->forceLoad();

        return array_filter($this->entities, $p);
    }

    /**
     * {@inheritdoc}
     */
    public function forAll(Closure $p)
    {
        $this->forceLoad();

        foreach ($this->entities as $entity) {
            if (!$p($entity)) {
                return false;
            }
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function map(Closure $func)
    {
        $this->forceLoad();

        return array_map($func, $this->entities);
    }

    /**
     * {@inheritdoc}
     */
    public function partition(Closure $p)
    {
        $this->forceLoad();

        $true = $false = [];

        foreach ($this->entities as $entity) {
            if ($p($entity)) {
                $true[] = $entity;
            } else {
                $false[] = $entity;
            }
        }

        return [$true, $false];
    }

    /**
     * {@inheritdoc}
     */
    public function indexOf($element)
    {
        $this->forceLoad();

        return array_search($element, $this->entities, true);
    }

    /**
     * {@inheritdoc}
     */
    public function slice($offset, $length = null)
    {
        $this->forceLoad();

        return array_slice($this->entities, $offset, $length);
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        $this->forceLoad();

        return new \ArrayIterator($this->entities);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset)
    {
        $this->forceLoad();

        return array_key_exists($offset, $this->entities);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        $this->initialize();

        if (!isset($this->entities[$offset])) {
            throw AuditedCollectionException::offsetIsNotDefined($offset);
        }

        $entity = $this->entities[$offset];

        if (is_object($entity)) {
            return $entity;
        } else {
            return $this->entities[$offset] = $this->resolve($entity);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value)
    {
        throw AuditedCollectionException::collectionIsReadOnly();
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset)
    {
        throw AuditedCollectionException::collectionIsReadOnly();
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        $this->initialize();

        return count($this->entities);
    }

    /**
     * @param $entity
     *
     * @return object
     * @throws \Somnambulist\EntityAudit\Exception\DeletedException
     * @throws \Somnambulist\EntityAudit\Exception\NoRevisionFoundException
     * @throws \Somnambulist\EntityAudit\Exception\NotAuditedException
     */
    protected function resolve($entity)
    {
        return $this->auditReader->find($this->class, $entity['keys'], $this->revision);
    }

    /**
     * Force load the collection
     */
    protected function forceLoad()
    {
        $this->initialize();

        foreach ($this->entities as $key => $entity) {
            if (is_array($entity)) {
                $this->entities[$key] = $this->resolve($entity);
            }
        }
    }

    /**
     * Initialize the collection
     */
    protected function initialize()
    {
        if (!$this->initialized) {
            $params = [];

            $sql  = 'SELECT MAX(' . $this->configuration->getRevisionFieldName() . ') as rev, ';
            $sql .= $this->configuration->getRevisionTypeFieldName() . ' AS revtype, ';
            $sql .= implode(', ', $this->metadata->getIdentifierColumnNames()) . ' ';
            if (isset($this->associationDefinition['indexBy'])) {
                $sql .= ', ' . $this->associationDefinition['indexBy'] . ' ';
            }
            $sql .= 'FROM ' . $this->configuration->getTableName($this->metadata) . ' t ';
            $sql .= 'WHERE ' . $this->configuration->getRevisionFieldName() . ' <= ' . $this->revision . ' ';

            foreach ($this->foreignKeys as $column => $value) {
                $sql .= 'AND ' . $column . ' = ? ';
                $params[] = $value;
            }

            //we check for revisions greater than current belonging to other entities
            $sql .= 'AND NOT EXISTS (SELECT * FROM ' . $this->configuration->getTableName($this->metadata) . ' st WHERE';

            //ids
            foreach ($this->metadata->getIdentifierColumnNames() as $name) {
                $sql .= ' st.' . $name . ' = t.' . $name . ' AND';
            }

            //foreigns
            $sql .= ' ((';

            //master entity query, not equals
            $notEqualParts = $nullParts = [];
            foreach ($this->foreignKeys as $column => $value) {
                $notEqualParts[] = $column . ' <> ?';
                $nullParts[]     = $column . ' IS NULL';
                $params[]        = $value;
            }

            $sql .= implode(' AND ', $notEqualParts) . ') OR (' . implode(' AND ', $nullParts) . '))';

            //revision
            $sql .= ' AND st.' . $this->configuration->getRevisionFieldName() . ' <= ' . $this->revision;
            $sql .= ' AND st.' . $this->configuration->getRevisionFieldName() . ' > t.' . $this->configuration->getRevisionFieldName();

            $sql .= ') ';
            //end of check for for belonging to other entities

            //check for deleted revisions older than requested
            $sql .= 'AND NOT EXISTS (SELECT * FROM ' . $this->configuration->getTableName($this->metadata) . ' sd WHERE';

            //ids
            foreach ($this->metadata->getIdentifierColumnNames() as $name) {
                $sql .= ' sd.' . $name . ' = t.' . $name . ' AND';
            }

            //revision
            $sql .= ' sd.' . $this->configuration->getRevisionFieldName() . ' <= ' . $this->revision;
            $sql .= ' AND sd.' . $this->configuration->getRevisionFieldName() . ' > t.' . $this->configuration->getRevisionFieldName();

            $sql .= ' AND sd.' . $this->configuration->getRevisionTypeFieldName() . ' = ?';
            $params[] = 'DEL';

            $sql .= ') ';
            //end check for deleted revisions older than requested

            $sql .= 'GROUP BY ' . implode(', ', $this->metadata->getIdentifierColumnNames()) ./*', '.$this->configuration->getRevisionTypeFieldName().*/
                    ' ';
            $sql .= 'HAVING ' . $this->configuration->getRevisionTypeFieldName() . ' <> ?';
            //add rev type parameter
            $params[] = 'DEL';

            $rows = $this->auditReader->getConnection()->fetchAll($sql, $params);

            foreach ($rows as $row) {
                $entity = [
                    'rev'     => $row['rev'],
                    'revtype' => $row['revtype'],
                ];

                unset($row['rev'], $row['revtype']);

                $entity['keys'] = $row;

                if (isset($this->associationDefinition['indexBy'])) {
                    $key = $row[$this->associationDefinition['indexBy']];
                    unset($entity['keys'][$this->associationDefinition['indexBy']]);
                    $this->entities[$key] = $entity;
                } else {
                    $this->entities[] = $entity;
                }
            }

            $this->initialized = true;
        }
    }
}
