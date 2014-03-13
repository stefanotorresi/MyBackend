<?php
/**
 * @author Stefano Torresi (http://stefanotorresi.it)
 * @license See the file LICENSE.txt for copying permission.
 * ************************************************
 */

namespace MyBackend\Test\Entity;

use MyBackend\Entity\Role;
use MyBackend\Entity\User;
use PHPUnit_Framework_TestCase as TestCase;

class UserTest extends TestCase
{
    /**
     * @dataProvider roleProvider
     * @param $role
     */
    public function testAddHasRemoveRole($role)
    {
        $user = new User();

        $this->assertTrue($user->addRole($role));
        $this->assertTrue($user->hasRole($role));
        $this->assertCount(1, $user->getRoles());

        $this->assertTrue($user->removeRole($role));
        $this->assertFalse($user->hasRole($role));
        $this->assertCount(0, $user->getRoles());
    }

    public function roleProvider()
    {
        return [
            [ new Role('test') ],
            [ 'test' ],
        ];
    }

    public function testTryingAddMultipleTimesReturnsFalse()
    {
        $role = new Role('test');
        $user = new User();
        $this->assertTrue($user->addRole($role));
        $this->assertFalse($user->addRole($role));
        $this->assertCount(1, $user->getRoles());
    }

    public function testTryingRemoveMultipleTimesReturnsFalse()
    {
        $role = new Role('test');
        $user = new User();
        $user->addRole($role);

        $this->assertTrue($user->removeRole($role));
        $this->assertFalse($user->removeRole($role));
    }
}
