<?php
/**
 *
 * @author Stefano Torresi (http://stefanotorresi.it)
 * @license See the file LICENSE.txt for copying permission.
 * ************************************************
 */

namespace MyBackend\Entity\Fixture;

use MyBackend\Entity;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Zend\Crypt\Password\Bcrypt;

class User extends AbstractFixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $bcrypt = new Bcrypt;
        $admin = new Entity\User();

        /** @var Entity\Role $adminRole  */
        $adminRole = $this->getReference('admin-role');

        $username = 'admin';
        $password = 'somepassword';

        $admin->setUsername($username);
        $admin->setPassword($bcrypt->create($password));
        $admin->addRole($adminRole);

        $manager->persist($admin);
        $manager->flush();
    }

    public function getDependencies()
    {
        return array('MyBackend\Entity\Fixture\Role');
    }
}
