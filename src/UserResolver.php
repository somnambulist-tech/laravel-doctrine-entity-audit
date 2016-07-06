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

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Guard;
use Somnambulist\Doctrine\Contracts\Identifiable;
use Somnambulist\Doctrine\Contracts\Nameable;
use Somnambulist\Doctrine\Contracts\UniversallyIdentifiable;

/**
 * Class UserResolver
 *
 * @package    Somnambulist\EntityAudit
 * @subpackage Somnambulist\EntityAudit\UserResolver
 * @author     Dave Redfern
 */
class UserResolver
{

    /**
     * @var Guard
     */
    protected $guard;

    /**
     * @var string
     */
    protected $unknownUnauthenticatedUser;

    /**
     * @var string
     */
    protected $unknownAuthenticatedUser;



    /**
     * Constructor.
     *
     * @param Guard  $guard
     * @param string $authenticatedUser
     * @param string $unauthenticatedUser
     */
    public function __construct(Guard $guard, $authenticatedUser = 'Unknown Authenticated User', $unauthenticatedUser = 'system')
    {
        $this->guard                      = $guard;
        $this->unknownAuthenticatedUser   = $authenticatedUser;
        $this->unknownUnauthenticatedUser = $unauthenticatedUser;
    }

    /**
     * @return string
     */
    public function getUnknownAuthenticatedUser()
    {
        return $this->unknownAuthenticatedUser;
    }

    /**
     * @return string
     */
    public function getUnknownUnauthenticatedUser()
    {
        return $this->unknownUnauthenticatedUser;
    }

    /**
     * @return string
     */
    public function resolve()
    {
        if (null !== $user = $this->guard->user()) {
            switch (true) {
                // favour (potentially) unchanging user credentials
                case $user instanceof UniversallyIdentifiable:
                    return $user->getUuid();

                case $user instanceof Authenticatable:
                    return $user->getAuthIdentifier();

                case $user instanceof Identifiable:
                    return $user->getId();

                case $user instanceof Nameable:
                    return $user->getName();
            }

            return $this->getUnknownAuthenticatedUser();
        }

        return $this->getUnknownUnauthenticatedUser();
    }
}
