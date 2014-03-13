<?php
/**
 *
 * @author Stefano Torresi (http://stefanotorresi.it)
 * @license See the file LICENSE.txt for copying permission.
 * ************************************************
 */

namespace MyBackend\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use ZfcRbac\Identity\IdentityInterface;
use ZfcUser\Entity\User as ZfcUser;

abstract class AbstractUser extends ZfcUser implements
    IdentityInterface,
    RbacUserInterface
{
    /**
     * @var ArrayCollection
     */
    protected $roles;

    /**
     * @param string $username
     * @param string $email
     * @param string $displayName
     * @param string $password
     * @param int    $state
     */
    public function __construct($username = null, $email = null, $displayName = null, $password = null, $state = null)
    {
        if ($username) {
            $this->setUsername($username);
        }

        if ($email) {
            $this->setEmail($email);
        }

        if ($displayName) {
            $this->setDisplayName($displayName);
        }

        if ($password) {
            $this->setPassword($password);
        }

        if ($state) {
            $this->setState($state);
        }

        $this->roles = new ArrayCollection();
    }

    /**
     * @return Collection
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * Add a role. If it's not a Role instance, creates a new one
     *
     * @param  Role|string $role
     * @return bool
     */
    public function addRole($role)
    {
        if (! $role instanceof Role) {
            $role = new Role($role);
        }

        if ($this->roles->contains($role)) {
            return false;
        }

        return $this->roles->add($role);
    }

    /**
     * @param  Role|string $role
     * @return bool
     */
    public function removeRole($role)
    {
        if (is_string($role)) {
            // @todo replace this with Criteria API as soon as it gets implemented for persistent collections on MtM
            // see https://github.com/doctrine/doctrine2/pull/885/
            $search = $this->roles->filter(function (Role $r) use ($role) {
                return $r->getName() === $role;
            })->toArray();
            $role = array_shift($search);
        }

        return $this->roles->removeElement($role);
    }

    /**
     * @param  Role|string $role
     * @return bool
     */
    public function hasRole($role)
    {
        if (is_string($role)) {
            // @todo replace this with Criteria API as soon as it gets implemented for persistent collections on MtM
            // see https://github.com/doctrine/doctrine2/pull/885/
            $search = $this->roles->filter(function (Role $r) use ($role) {
                return $r->getName() === $role;
            })->toArray();
            $role = array_shift($search);
        }

        return $this->roles->contains($role);
    }
}
