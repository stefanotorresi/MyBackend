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
    const CAN_LOGIN_AS_ADMIN = 'can-login-as-admin';
    const CAN_USE_ADMIN_DASHBOARD = 'can-use-admin-dashboard';

    public function load(ObjectManager $manager)
    {
        $permissions = [
            new Permission(static::CAN_LOGIN_AS_ADMIN),
            new Permission(static::CAN_USE_ADMIN_DASHBOARD),
        ];

        foreach ($permissions as $permission) {
            $this->addReference($permission->getName().'-permission', $permission);
            $manager->persist($permission);
        }

        $manager->flush();
    }
}
