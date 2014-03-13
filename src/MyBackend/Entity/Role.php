<?php
/**
 * @author  ZF-Commons
 * @license See the file LICENSE.txt for copying permission.
 * ************************************************
 */

namespace MyBackend\Entity;

use Doctrine\Common\Collections;
use MyBase\Entity\Entity;
use ZfcRbac\Permission\PermissionInterface;

class Role extends Entity implements RoleInterface
{
    /**
     * @var string|null
     */
    protected $name;

    /**
     * @var PermissionInterface[]|Collections\Collection
     */
    protected $permissions;

    /**
     * Init the Doctrine collection
     */
    public function __construct($name)
    {
        $this->setName($name);
        $this->permissions = new Collections\ArrayCollection();
    }

    /**
     * Set the role name
     *
     * @param  string $name
     * @return self
     */
    public function setName($name)
    {
        $this->name = (string) $name;

        return $this;
    }

    /**
     * Get the role name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getPermissions()
    {
        return $this->permissions;
    }

    /**
     * Add a permission. If it's not a Permission instance, creates a new one
     *
     * @param  Permission|string $permission
     * @return bool
     */
    public function addPermission($permission)
    {
        if (! $permission instanceof Permission) {
            $permission = new Permission($permission);
        }

        if ($this->permissions->contains($permission)) {
            return false;
        }

        return $this->permissions->add($permission);
    }

    /**
     * @param  Permission|string $permission
     * @return bool
     */
    public function removePermission($permission)
    {
        if (is_string($permission)) {
            // @todo replace this with Criteria API as soon as it gets implemented for persistent collections on MtM
            // see https://github.com/doctrine/doctrine2/pull/885/
            $search = $this->permissions->filter(function (Permission $p) use ($permission) {
                return $p->getName() === $permission;
            })->toArray();
            $permission = array_shift($search);
        }

        return $this->permissions->removeElement($permission);
    }

    /**
     * @param  Permission|string $permission
     * @return bool
     */
    public function hasPermission($permission)
    {
        if (is_string($permission)) {
            // @todo replace this with Criteria API as soon as it gets implemented for persistent collections on MtM
            // see https://github.com/doctrine/doctrine2/pull/885/
            $search = $this->permissions->filter(function (Permission $p) use ($permission) {
                return $p->getName() === $permission;
            })->toArray();
            $permission = array_shift($search);
        }

        return $this->permissions->contains($permission);
    }

    /**
     * {@inheritDoc}
     */
    public function __toString()
    {
        return $this->getName();
    }
}
