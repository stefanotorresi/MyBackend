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
     *      inverseJoinColumns={@ORM\JoinColumn(name="role_id", referencedColumnName="id")}
     * )
     * @var ArrayCollection
     */
    protected $roles;

    public function __construct()
    {
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
     * @param  Role  $role
     * @return $this
     */
    public function addRole(Role $role)
    {
        $this->roles->add($role);

        return $this;
    }
}
