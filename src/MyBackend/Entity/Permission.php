<?php
/**
 * @author  ZF-Commons
 * @license See the file LICENSE.txt for copying permission.
 * ************************************************
 */

namespace MyBackend\Entity;

use Doctrine\ORM\Mapping as ORM;
use MyBase\Entity\Entity;
use ZfcRbac\Permission\PermissionInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="mbe_permissions")
 */
class Permission extends Entity implements PermissionInterface
{
    /**
     * @var string
     *
     * @ORM\Column(type="string", length=128, unique=true)
     */
    protected $name;

    /**
     * Constructor
     */
    public function __construct($name)
    {
        $this->setName($name);
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
     * {@inheritDoc}
     */
    public function __toString()
    {
        return $this->name;
    }
}
