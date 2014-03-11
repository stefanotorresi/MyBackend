<?php
/**
 * @author  ZF-Commons
 * @license See the file LICENSE.txt for copying permission.
 * ************************************************
 */

namespace MyBackend\Entity;

use Doctrine\Common\Collections;
use Doctrine\ORM\Mapping as ORM;
use MyBase\Entity\Entity;
use Rbac\Role\RoleInterface;
use ZfcRbac\Permission\PermissionInterface;

/**
 * @ORM\Entity(repositoryClass="MyBackend\Mapper\Doctrine\DoctrineRoleMapper")
 * @ORM\Table(name="mbe_roles")
 */
class Role extends Entity implements RoleInterface
{
    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=32, unique=true)
     */
    protected $name;

    /**
     * @var PermissionInterface[]|Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Permission", indexBy="name", cascade={"persist"}, fetch="EAGER")
     * @ORM\JoinTable(name="mbe_roles_permissions")
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
     * Add a permission
     *
     * @param  PermissionInterface|string $permission
     * @return self
     */
    public function addPermission($permission)
    {
        if (is_string($permission)) {
            $permission = new Permission($permission);
        }

        $this->permissions[(string) $permission] = $permission;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function hasPermission($permission)
    {
        return isset($this->permissions[(string) $permission]);
    }
}
