<?php

namespace Somnambulist\EntityAudit\Tests\Fixtures\Issue;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Issue156Client
 * @package Somnambulist\EntityAudit\Tests\Fixtures\Issue
 * @ORM\Entity()
 */
class Issue156Client extends Issue156Contact
{
    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $clientSpecificField;

    /**
     * @param string $clientSpecificField
     * @return $this
     */
    public function setClientSpecificField($clientSpecificField)
    {
        $this->clientSpecificField = $clientSpecificField;

        return $this;
    }

    /**
     * @return string
     */
    public function getClientSpecificField()
    {
        return $this->clientSpecificField;
    }
}
