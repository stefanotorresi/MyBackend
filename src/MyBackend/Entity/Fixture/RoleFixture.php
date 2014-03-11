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
        $canLoginAsAdmin      = $this->getReference(PermissionFixture::CAN_LOGIN_AS_ADMIN . '-permission');
        $canUseAdminDashboard = $this->getReference(PermissionFixture::CAN_USE_ADMIN_DASHBOARD . '-permission');

        $roles = [
            (new Entity\Role(static::ADMIN))->addPermission($canUseAdminDashboard),
            (new Entity\Role(static::GUEST))->addPermission($canLoginAsAdmin)
        ];

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
