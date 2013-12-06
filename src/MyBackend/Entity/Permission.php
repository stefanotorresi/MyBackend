<?php
/**
 * @author  ZF-Commons
 * @license See the file LICENSE.txt for copying permission.
 * ************************************************
 */

namespace MyBackend\Entity;

use Doctrine\Common\Collections;
use Doctrine\ORM\Mapping as ORM;
use ZfcRbac\Permission\PermissionInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="mbe_permissions")
 */
class Permission implements PermissionInterface
{
    /**
     * @var int|null
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=32)
     */
    protected $name;

    /**
     * @var Role[]|Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Role", mappedBy="permissions")
     */
    protected $roles;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->roles = new Collections\ArrayCollection();
    }

    /**
     * Get the permission identifier
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the permission name
     *
     * @param  string $name
     * @return void
     */
    public function setName($name)
    {
        $this->name = (string) $name;
    }

    /**
     * Get the permission name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param Role $role
     */
    public function addRole(Role $role)
    {
        $this->roles->add($role);
    }

    /**
     * Get the roles
     *
     * @return Role[]
     */
    public function getRoles()
    {
        return $this->roles;
    }
}
