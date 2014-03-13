<?php
/**
 * @author Stefano Torresi (http://stefanotorresi.it)
 * @license See the file LICENSE.txt for copying permission.
 * ************************************************
 */

namespace MyBackend\Mapper\Doctrine;

use MyBackend\Mapper\RoleMapperInterface;
use MyBase\Doctrine\EntityMapper;

class DoctrineRoleMapper extends EntityMapper implements RoleMapperInterface
{
    /**
     * Concrete implementation to honour the contract, can't rely on doctrine findBy magic methods
     *
     * @param  string                                      $name
     * @return \MyBackend\Entity\RoleInterface|null|object
     */
    public function findOneByName($name)
    {
        return $this->findOneBy(['name' => $name]);
    }
}
