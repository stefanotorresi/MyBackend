<?php
/**
 * @author Stefano Torresi (http://stefanotorresi.it)
 * @license See the file LICENSE.txt for copying permission.
 * ************************************************
 */

namespace MyBackend\Test\Entity;

use MyBackend\Entity\Permission;
use MyBackend\Entity\Role;
use PHPUnit_Framework_TestCase as TestCase;

class RoleTest extends TestCase
{
    /**
     * @dataProvider roleProvider
     * @param $permission
     */
    public function testAddHasRemovePermission($permission)
    {
        $role = new Role('role');

        $this->assertTrue($role->addPermission($permission));
        $this->assertTrue($role->hasPermission($permission));
        $this->assertCount(1, $role->getPermissions());

        $this->assertTrue($role->removePermission($permission));
        $this->assertFalse($role->hasPermission($permission));
        $this->assertCount(0, $role->getPermissions());
    }

    public function roleProvider()
    {
        return [
            [ new Permission('test') ],
            [ 'test' ],
        ];
    }

    public function testTryingAddMultipleTimesReturnsFalse()
    {
        $permission = new Permission('test');
        $role = new Role('role');
        $this->assertTrue($role->addPermission($permission));
        $this->assertFalse($role->addPermission($permission));
        $this->assertCount(1, $role->getPermissions());
    }

    public function testTryingRemoveMultipleTimesReturnsFalse()
    {
        $permission = new Permission('test');
        $role = new Role('role');
        $role->addPermission($permission);

        $this->assertTrue($role->removePermission($permission));
        $this->assertFalse($role->removePermission($permission));
    }
}
