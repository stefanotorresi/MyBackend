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
    const ADMIN = 'admin';
    const GUEST = 'guest';

    public function load(ObjectManager $manager)
    {
        $adminAccess = $this->getReference(PermissionFixture::ADMIN_ACCESS . '-permission');
        $guestAccess = $this->getReference(PermissionFixture::GUEST_ACCESS . '-permission');

        $adminRole = new Entity\Role(static::ADMIN);
        $adminRole->addPermission($adminAccess);

        $guestRole = new Entity\Role(static::GUEST);
        $guestRole->addPermission($guestAccess);

        $roles = [ $adminRole , $guestRole ];

        foreach ($roles as $role) {
            if (! $this->hasReference($role->getName().'-role')) {
                $this->addReference($role->getName().'-role', $role);
            }
            $manager->persist($role);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return ['MyBackend\Entity\Fixture\PermissionFixture'];
    }
}
