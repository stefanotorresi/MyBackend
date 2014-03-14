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
use Traversable;
use Zend\Stdlib\Guard\ArrayOrTraversableGuardTrait;
use ZfcUser\Service\User as ZfcUserService;

/**
 * Class UserService
 * @package MyBackend\Service
 * @method UserMapperInterface getUserMapper()
 */
class UserService extends ZfcUserService implements RbacUserServiceInterface
{
    use ArrayOrTraversableGuardTrait;

    /**
     * @var RoleMapperInterface
     */
    protected $roleMapper;

    /**
     * @param array|Traversable|string|RoleInterface $roles
     * @param RbacUserInterface $user
     * @param bool              $update
     */
    public function addRolesToUser($roles, RbacUserInterface $user, $update = true)
    {
        if (is_string($roles) || $roles instanceof RoleInterface) {
            $roles = [ $roles ];
        }

        $this->guardForArrayOrTraversable($roles, '$roles');

        foreach ($roles as $role) {
            if (! $role instanceof RoleInterface) {
                $existingRole = $this->getRoleMapper()->findOneByName((string) $role);
                $role = $existingRole ?: $role;
            }
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
