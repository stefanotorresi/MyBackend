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

class RoleFixture extends AbstractFixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $adminLoginPermission = $this->getReference('admin-login-permission');
        $adminDashboardPermission = $this->getReference('admin-dashboard-permission');

        $roles = [
            (new Entity\Role('admin'))->addPermission($adminDashboardPermission),
            (new Entity\Role('guest'))->addPermission($adminLoginPermission)
        ];

        foreach ($roles as $key => $role) {
            $this->addReference($key.'-role', $role);
            $manager->persist($role);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return ['MyBackend\Entity\Fixture\PermissionFixture'];
    }
}
