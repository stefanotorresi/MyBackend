<?php
/**
 * @author Stefano Torresi (http://stefanotorresi.it)
 * @license See the file LICENSE.txt for copying permission.
 * ************************************************
 */

namespace MyBackend\Service;

use MyBackend\Entity\RbacUserInterface;
use MyBackend\Entity\Role;
use MyBackend\Mapper\RoleMapperInterface;
use MyBackend\Mapper\UserMapperInterface;
use Rbac\Role\RoleInterface;
use ZfcUser\Service\User as ZfcUserService;

/**
 * Class UserService
 * @package MyBackend\Service
 * @method UserMapperInterface getUserMapper()
 */
class UserService extends ZfcUserService implements RbacUserServiceInterface
{
    /**
     * @var RoleMapperInterface
     */
    protected $roleMapper;

    /**
     * Adds a role to a user. If role doesn't exists yet, creates a new one.
     *
     * @param  RoleInterface|string           $role
     * @param  RbacUserInterface              $user
     * @param  bool                           $update
     * @throws Exception\UserServiceException
     */
    public function addRoleToUser($role, RbacUserInterface $user, $update = true)
    {
        if (! $role instanceof RoleInterface) {
            $role = $this->getRoleMapper()->findOneByName((string) $role) ?: $role;
        }

        $user->addRole($role);

        if ($update) {
            $this->getUserMapper()->update($user);
        }
    }

    /**
     * @param $roles
     * @param RbacUserInterface $user
     * @param bool              $update
     */
    public function addRoleListToUser($roles, RbacUserInterface $user, $update = true)
    {
        foreach ($roles as $role) {
            $user->addRole($role);
        }

        if ($update) {
            $this->getUserMapper()->update($user);
        }
    }

    /**
     * @return RoleMapperInterface
     */
    public function getRoleMapper()
    {
        if (null === $this->roleMapper) {
            $this->roleMapper = $this->getServiceManager()->get('MyBackend\Mapper\RoleMapper');
        }

        return $this->roleMapper;
    }

    /**
     * @param RoleMapperInterface $roleMapper
     */
    public function setRoleMapper(RoleMapperInterface $roleMapper)
    {
        $this->roleMapper = $roleMapper;
    }
}
