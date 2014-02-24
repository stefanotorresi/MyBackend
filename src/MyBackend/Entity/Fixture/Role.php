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

class Role extends AbstractFixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        /** @var Entity\Permission $adminPermission  */
        $adminPermission = $this->getReference('admin-permission');

        $roles['admin'] = new Entity\Role('admin');
        $roles['admin']->addPermission($adminPermission);

        $roles['guest'] = new Entity\Role('guest');

        foreach ($roles as $key => $role) {
            $this->addReference($key.'-role', $role);
            $manager->persist($role);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return ['MyBackend\Entity\Fixture\Permission'];
    }
}
