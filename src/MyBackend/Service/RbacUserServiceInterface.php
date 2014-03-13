<?php
/**
 * @author Stefano Torresi (http://stefanotorresi.it)
 * @license See the file LICENSE.txt for copying permission.
 * ************************************************
 */

namespace MyBackend\Service;

use MyBackend\Entity\RbacUserInterface;
use MyBackend\Mapper\RoleMapperInterface;

interface RbacUserServiceInterface
{
    /**
     * @return RoleMapperInterface
     */
    public function getRoleMapper();

    /**
     * @param RoleMapperInterface $roleMapper
     */
    public function setRoleMapper(RoleMapperInterface $roleMapper);

    /**
     * @param  $role
     * @param RbacUserInterface $user
     */
    public function addRoleToUser($role, RbacUserInterface $user);

    /**
     * @param  $roles
     * @param RbacUserInterface $user
     */
    public function addRoleListToUser($roles, RbacUserInterface $user);
}
