<?php

namespace Somnambulist\EntityAudit;

/**
 * Interface UserResolverInterface
 *
 * @package    Somnambulist\EntityAudit
 * @subpackage Somnambulist\EntityAudit\UserResolverInterface
 */
interface UserResolverInterface
{

    /**
     * Returns a string representing the current user
     *
     * How the user is resolved is left up to the implementation. This would usually
     * be pulled from the Laravels Guard class, but could be some other source.
     * Optionally: default names may be used - see the UserResolver implementation.
     *
     * @return string
     */
    public function resolve();
}
