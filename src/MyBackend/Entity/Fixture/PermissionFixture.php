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
    const ADMIN_ACCESS = 'admin-access';
    const GUEST_ACCESS  = 'guest-access';

    public function load(ObjectManager $manager)
    {
        $permissions = [
            new Permission(static::ADMIN_ACCESS),
            new Permission(static::GUEST_ACCESS),
        ];

        foreach ($permissions as $permission) {
            $this->addReference($permission->getName().'-permission', $permission);
            $manager->persist($permission);
        }

        $manager->flush();
    }
}
