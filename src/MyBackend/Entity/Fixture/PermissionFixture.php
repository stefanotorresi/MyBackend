<?php
/**
 *
 * @author Stefano Torresi (http://stefanotorresi.it)
 * @license See the file LICENSE.txt for copying permission.
 * ************************************************
 */

namespace MyBackend\Entity\Fixture;

use MyBackend\Entity\Permission;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;

class PermissionFixture extends AbstractFixture
{
    public function load(ObjectManager $manager)
    {
        $permissions = [
            new Permission('admin-login'),
            new Permission('admin-dashboard'),
        ];

        foreach ($permissions as $permission) {
            $this->addReference($permission->getName().'-permission', $permission);
            $manager->persist($permission);
        }

        $manager->flush();
    }
}
