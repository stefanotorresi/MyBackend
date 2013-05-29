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
use RecursiveIteratorIterator;
use Zend\Permissions\Rbac\Role as ZendRole;
use Zend\Permissions\Rbac\RoleInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="roles")
 */
class Role extends ZendRole
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", name="role_id");
     * @ORM\GeneratedValue(strategy="AUTO")
     * @var int
     */
    protected $id;

    /**
     * @ORM\Column(type="string",length=100,unique=true);
     * @var string
     */
    protected $name;

    /**
     * @ORM\ManyToOne(targetEntity="Role")
     * @ORM\JoinColumn(name="parent_role_id", referencedColumnName="role_id")
     * @var int
     */
    protected $parent;

    /**
     * @ORM\ManyToMany(targetEntity="Permission", cascade={"persist"})
     * @ORM\JoinTable(
     *      name="roles_permissions",
     *      joinColumns={@ORM\JoinColumn(name="role_id", referencedColumnName="role_id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="permission_id", referencedColumnName="permission_id")}
     * )
     * @var ArrayCollection
     */
    protected $permissions;

    /**
     * @param string $name
     */
    public function __construct($name = null)
    {
        parent::__construct($name);

        $this->permissions = new ArrayCollection();
    }


    /**
     * Add permission to the role.
     *
     * @param string|Permission $permission
     * @return Role
     */
    public function addPermission($permission)
    {
        if ( ! $permission instanceof Permission ) {
            $permission = new Permission($permission);
        }

        $this->permissions->add($permission);

        return $this;
    }

    /**
     * Checks if a permission exists for this role or any child roles.
     *
     * @param  string|Permission $name
     * @return bool
     */
    public function hasPermission($name)
    {
        if ($name instanceof Permission && $this->permissions->contains($name)) {
            return true;
        }

        if (is_string($name)) {
            /** @var Permission $permission */
            foreach ($this->permissions as $permission) {
                if ($permission->getName() === $name) {
                    return true;
                }
            }
        }

        $it = new RecursiveIteratorIterator($this, RecursiveIteratorIterator::CHILD_FIRST);
        foreach ($it as $leaf) {
            /** @var RoleInterface $leaf */
            if ($leaf->hasPermission($name)) {
                return true;
            }
        }

        return false;
    }
}
