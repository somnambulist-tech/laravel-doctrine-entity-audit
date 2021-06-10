<?php

namespace Somnambulist\EntityAudit\Tests\Fixtures\Issue;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class IssueReservedSQLKeywordsAsColumnNames
 * @package Somnambulist\EntityAudit\Tests\Fixtures\Issue
 * @ORM\Entity()
 */
class IssueReservedSQLKeywordsAsColumnNames
{
    /** @ORM\Id @ORM\Column(type="integer") @ORM\GeneratedValue(strategy="AUTO") */
    protected $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=false, name="`default`")
     */
    private $default;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=false, name="`order`")
     */
    private $order;

    /**
     * @param mixed $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getDefault(): string
    {
        return $this->default;
    }

    /**
     * @param string $default
     *
     * @return $this
     */
    public function setDefault($default)
    {
        $this->default = $default;

        return $this;
    }

    /**
     * @return int
     */
    public function getOrder(): int
    {
        return $this->order;
    }

    /**
     * @param int $order
     *
     * @return $this
     */
    public function setOrder($order)
    {
        $this->order = $order;

        return $this;
    }
}
