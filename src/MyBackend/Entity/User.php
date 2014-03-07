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
 * @ORM\AttributeOverrides({
 *      @ORM\AttributeOverride(name="password",
 *          column=@ORM\Column(
 *              name     = "password",
 *              type     = "string",
 *              length   = 128,
 *              nullable = true
 *          )
 *      )
 * })
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
     * @param  Role  $role
     * @return $this
     */
    public function addRole(Role $role)
    {
        $this->roles->add($role);

        return $this;
    }
}
