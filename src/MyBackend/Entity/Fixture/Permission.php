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

class Permission extends AbstractFixture
{
    public function load(ObjectManager $manager)
    {
        $adminPermission = new Entity\Permission('admin');

        $this->addReference('admin-permission', $adminPermission);

        $manager->persist($adminPermission);
        $manager->flush();
    }
}
