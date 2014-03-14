<?php
/**
 * @author Stefano Torresi (http://stefanotorresi.it)
 * @license See the file LICENSE.txt for copying permission.
 * ************************************************
 */

namespace MyBackend\Service;

use MyBackend\Entity\RbacUserInterface;
use MyBackend\Entity\RoleInterface;
use MyBackend\Mapper\RoleMapperInterface;
use Traversable;

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
     * @param array|Traversable|string|RoleInterface $roles
     * @param RbacUserInterface                      $user
     */
    public function addRolesToUser($roles, RbacUserInterface $user);
}
