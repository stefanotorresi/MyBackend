<?php
/**
 *
 * @author Stefano Torresi (http://stefanotorresi.it)
 * @license See the file LICENSE.txt for copying permission.
 * ************************************************
 */

namespace MyBackend\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use ZfcRbac\Identity\IdentityInterface;
use ZfcUser\Entity\User as ZfcUser;

/**
 * @ORM\Entity
 * @ORM\Table(name="mbe_users")
 */
class User extends ZfcUser implements IdentityInterface
{
    /**
     * @ORM\ManyToMany(targetEntity="Role", cascade={"persist"})
     * @ORM\JoinTable(
     *      name="mbe_users_roles",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="user_id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="role_id", referencedColumnName="role_id")}
     * )
     * @var ArrayCollection
     */
    protected $roles;

    public function __construct()
    {
        $this->roles = new ArrayCollection();
    }

    /**
     * returns an array of role names, as expected by Zend Rbac
     *
     * @return array
     */
    public function getRoles()
    {
        $roleNames = array();

        foreach ($this->roles as $role) {
            /** @var Role $role  */
            $roleNames[] = $role->getName();
        }

        return $roleNames;
    }

    /**
     * @param Role $role
     * @return $this
     */
    public function addRole(Role $role)
    {
        $this->roles->add($role);

        return $this;
    }
}
