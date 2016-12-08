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

namespace Somnambulist\EntityAudit;

/**
 * Class AuditRegistry
 *
 * @package    Somnambulist\EntityAudit
 * @subpackage Somnambulist\EntityAudit\AuditRegistry
 * @author     Dave Redfern
 */
final class AuditRegistry implements \Countable, \IteratorAggregate
{

    /**
     * @var string
     */
    protected $defaultEntityManagerName = 'default';

    /**
     * @var array|AuditManager[]
     */
    protected $managers = [];



    /**
     * Constructor.
     *
     * @param array|AuditManager[] $managers
     */
    public function __construct(array $managers = [])
    {
        $this->managers = $managers;
    }

    /**
     * @inheritDoc
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->managers);
    }

    /**
     * @inheritDoc
     */
    public function count()
    {
        return count($this->managers);
    }

    /**
     * @return string
     */
    public function getDefaultEntityManagerName()
    {
        return $this->defaultEntityManagerName;
    }

    /**
     * @param string $defaultEntityManagerName
     *
     * @return $this
     */
    public function setDefaultEntityManagerName($defaultEntityManagerName)
    {
        $this->defaultEntityManagerName = $defaultEntityManagerName;

        return $this;
    }



    /**
     * @param string       $entityManagerName
     * @param AuditManager $manager
     *
     * @return $this
     */
    public function add($entityManagerName, AuditManager $manager)
    {
        $this->managers[$entityManagerName] = $manager;

        return $this;
    }

    /**
     * @param string $entityManagerName
     *
     * @return bool
     */
    public function has($entityManagerName)
    {
        return array_key_exists($entityManagerName, $this->managers);
    }

    /**
     * @param string $entityManagerName
     *
     * @return AuditManager
     */
    public function get($entityManagerName = null)
    {
        $entityManagerName = $entityManagerName ?: $this->getDefaultEntityManagerName();

        if (!$this->has($entityManagerName)) {
            throw new \InvalidArgumentException(
                sprintf('No AuditManager has been configured for "%s"', $entityManagerName)
            );
        }

        return $this->managers[$entityManagerName];
    }

    /**
     * @param string $entityManagerName
     *
     * @return $this
     */
    public function remove($entityManagerName)
    {
        unset($this->managers[$entityManagerName]);

        return $this;
    }
}
