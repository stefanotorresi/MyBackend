<?php
/**
 * @author  ZF-Commons
 * @license See the file LICENSE.txt for copying permission.
 * ************************************************
 */

namespace MyBackend\Entity;

use Doctrine\Common\Collections;
use Doctrine\ORM\Mapping as ORM;
use Zend\Permissions\Rbac\AbstractRole;
use ZfcRbac\Permission\PermissionInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="mbe_roles")
 */
class Role extends AbstractRole
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
     * @ORM\Column(type="string", length=32, unique=true)
     */
    protected $name;

    /**
     * @var Role
     *
     * @ORM\ManyToOne(targetEntity="Role")
     */
    protected $parent;

    /**
     * @var PermissionInterface[]|Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Permission", indexBy="name", inversedBy="roles", cascade={"persist"})
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
     * Get the role identifier
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the role name
     *
     * @param  string $name
     * @return void
     */
    public function setName($name)
    {
        $this->name = (string) $name;
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
     * Set the parent role
     *
     * @param  string|Role $parent
     * @return void
     */
    public function setParent($parent)
    {
        $this->parent = $parent;
    }

    /**
     * Get the parent role
     *
     * @return Role
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Add a permission
     *
     * @param  PermissionInterface|string $permission
     * @return void
     */
    public function addPermission($permission)
    {
        if (is_string($permission)) {
            $permission = new Permission($permission);
        }

        $permission->addRole($this);
        $this->permissions[$permission->getName()] = $permission;
    }
}
