<?php
/**
 * @author Stefano Torresi (http://stefanotorresi.it)
 * @license See the file LICENSE.txt for copying permission.
 * ************************************************
 */

namespace MyBackend\Mapper;

use MyBackend\Entity\User;
use ZfcUserDoctrineORM\Mapper\User as DoctrineUserMapper;

class UserMapper extends DoctrineUserMapper
{
    public function delete($whereOrEntity, $tableName = null)
    {
        if ($whereOrEntity instanceof User) {
            $this->em->remove($whereOrEntity);
            $this->em->flush();
        } else {
            return parent::delete($whereOrEntity, $tableName);
        }
    }
}
