<?php
/**
 * @author Stefano Torresi (http://stefanotorresi.it)
 * @license See the file LICENSE.txt for copying permission.
 * ************************************************
 */

namespace MyBackend\Mapper;

use ZfcUser\Mapper\UserInterface as ZfcUserMapperInterface;

interface UserMapperInterface extends ZfcUserMapperInterface
{
    public function remove($entity);
}
