<?php
/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the LGPL-2.1 license.
 */

namespace Somnambulist\EntityAudit\Support;

use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Illuminate\Support\Collection;

/**
 * Class TableConfiguration
 *
 * @package    Somnambulist\EntityAudit
 * @subpackage Somnambulist\EntityAudit\Support\TableConfiguration
 * @author     Dave Redfern
 */
class TableConfiguration
{

    /**
     * @var Collection
     */
    protected $config;


    /**
     * Constructor.
     *
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->config = new Collection($config);
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->config->toArray();
    }

    /**
     * @param string     $name
     * @param null|mixed $default
     *
     * @return mixed
     */
    public function get($name, $default = null)
    {
        return $this->config->get($name, $default);
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
        return $this->get('table_prefix', 'revision_audit_');
    }

    /**
     * @return string
     */
    public function getTableSuffix()
    {
        return $this->get('table_suffix', '');
    }

    /**
     * @return string
     */
    public function getRevisionFieldName()
    {
        return $this->get('revision_field_name', 'rev');
    }

    /**
     * @return string
     */
    public function getRevisionTypeFieldName()
    {
        return $this->get('revision_type_field_name', 'revtype');
    }

    /**
     * @return string
     */
    public function getRevisionTableName()
    {
        return $this->get('revision_table_name', 'revisions');
    }

    /**
     * @return string
     */
    public function getRevisionIdFieldType()
    {
        return $this->get('revision_id_field_type', 'bigint');
    }
}
