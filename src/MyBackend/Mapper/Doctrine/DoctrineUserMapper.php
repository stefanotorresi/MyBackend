<?php
/**
 * @author Stefano Torresi (http://stefanotorresi.it)
 * @license See the file LICENSE.txt for copying permission.
 * ************************************************
 */

namespace MyBackend\Mapper\Doctrine;

use MyBackend\Mapper\UserMapperInterface;
use MyBase\Doctrine\EntityMapper;

class DoctrineUserMapper extends EntityMapper implements UserMapperInterface
{
    public function findByEmail($email)
    {
        return $this->findOneBy(array('email' => $email));
    }

    public function findByUsername($username)
    {
        return $this->findOneBy(array('username' => $username));
    }

    public function findById($id)
    {
        return $this->find($id);
    }

    public function insert($entity)
    {
        return $this->save($entity);
    }

    public function update($entity)
    {
        return $this->save($entity);
    }

}
