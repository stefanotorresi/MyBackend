<?php
/**
 * @author Stefano Torresi (http://stefanotorresi.it)
 * @license See the file LICENSE.txt for copying permission.
 * ************************************************
 */

namespace MyBackend\Mapper;

use MyBackend\Entity\RoleInterface;
use MyBase\DataMapper\MapperInterface;

interface RoleMapperInterface extends MapperInterface
{
    /**
     * @param  string        $name
     * @return RoleInterface
     */
    public function findOneByName($name);
}
