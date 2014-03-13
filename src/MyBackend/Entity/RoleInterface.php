<?php
/**
 * @author Stefano Torresi (http://stefanotorresi.it)
 * @license See the file LICENSE.txt for copying permission.
 * ************************************************
 */

namespace MyBackend\Entity;

use Doctrine\Common\Collections\Collection;
use Rbac\Role\RoleInterface as RbacRoleInterface;
use ZfcRbac\Permission\PermissionInterface;

interface RoleInterface extends RbacRoleInterface
{
    /**
     * @return Collection|PermissionInterface[]
     */
    public function getPermissions();

    /**
     * @param $permission
     * @return bool
     */
    public function addPermission($permission);

    /**
     * @param $permission
     * @return bool
     */
    public function hasPermission($permission);

    /**
     * @param $permission
     * @return bool
     */
    public function removePermission($permission);

    /**
     * @return string
     */
    public function __toString();
}
