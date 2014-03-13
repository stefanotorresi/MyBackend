<?php
/**
 * @author Stefano Torresi (http://stefanotorresi.it)
 * @license See the file LICENSE.txt for copying permission.
 * ************************************************
 */

namespace MyBackend\Entity;

interface RbacUserInterface
{
    public function getRoles();
    public function addRole($role);
    public function hasRole($role);
    public function removeRole($role);
}
