<?php
/**
 * @author Stefano Torresi (http://stefanotorresi.it)
 * @license See the file LICENSE.txt for copying permission.
 * ************************************************
 */

namespace MyBackend\Mapper\Doctrine;

use Doctrine\ORM\EntityRepository;
use MyBackend\Mapper\RoleMapperInterface;
use MyBase\Doctrine\EntityMapperTrait;

class DoctrineRoleMapper extends EntityRepository implements RoleMapperInterface
{
    use EntityMapperTrait;
}
