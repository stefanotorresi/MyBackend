<?php
/**
 * @author Stefano Torresi (http://stefanotorresi.it)
 * @license See the file LICENSE.txt for copying permission.
 * ************************************************
 */

namespace MyBackend\Test\Integration;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use MyBackend\Entity\Permission;
use MyBackend\Entity\Role;
use MyBackend\Entity\AbstractUser;
use MyBackend\Service\UserService;
use MyBackend\Test\Bootstrap;
use PHPUnit_Framework_TestCase as TestCase;
use Zend\Authentication\AuthenticationService;
use Zend\Http\Request;
use Zend\ServiceManager\ServiceManager;
use ZfcRbac\Service\AuthorizationService;
use ZfcUser\Authentication\Adapter\AdapterChain;

class RbacIntegrationTest extends TestCase
{
    /**
     * @var ServiceManager
     */
    protected $serviceManager;

    /**
     * @var AuthenticationService
     */
    protected $authenticationService;

    public function setUp()
    {
        require_once __DIR__ . '/_files/session_regenerate_id.php';

        $serviceManager = Bootstrap::getServiceManager();

        $serviceManager->get('Application')->bootstrap();

        /** @var EntityManager $entityManager */
        $entityManager = $serviceManager->get('Doctrine\ORM\EntityManager');

        /** @var UserService $userService */
        $userService = $serviceManager->get('MyBackend\Service\UserService');

        /** @var AuthenticationService $authenticationService */
        $authenticationService = $serviceManager->get('zfcuser_auth_service');

        $classes    = $entityManager->getMetadataFactory()->getAllMetadata();
        $schemaTool = new SchemaTool($entityManager);
        $schemaTool->dropDatabase();
        $schemaTool->createSchema($classes);

        $userData = [
            'username'       => 'testuser',
            'email'          => 'email@google.com',
            'display_name'   => '',
            'password'       => 'password',
            'passwordVerify' => 'password',
        ];
        $user = $userService->register($userData);

        if (! $user instanceof AbstractUser) {
            $this->markTestIncomplete('Could not register test user');
        }

        $guestRole = new Role('guest');
        $guestPermission = new Permission('guest-permission');
        $guestRole->addPermission($guestPermission);
        $adminPermission = new Permission('admin-permission');
        $adminRole = new Role('admin');
        $adminRole->addPermission($adminPermission);
        $user->addRole($guestRole);
        $userService->getRoleMapper()->save($guestRole, false);
        $userService->getRoleMapper()->save($adminRole);
        $userService->getUserMapper()->update($user);

        $authRequest = new Request();
        $authRequest->getPost()->set('identity', $userData['username'])
                               ->set('credential', $userData['password']);

        /** @var AdapterChain $adapter */
        $adapter = $authenticationService->getAdapter();
        $adapter->prepareForAuthentication($authRequest);
        $authenticationService->authenticate();

        $this->serviceManager        = $serviceManager;
        $this->authenticationService = $authenticationService;
    }

    public function testAuthentication()
    {
        $this->assertTrue($this->authenticationService->hasIdentity(), 'AuthenticationService has no identity');
    }

    public function testAuthorization()
    {
        /** @var AuthorizationService $authorizationService */
        $authorizationService = $this->serviceManager->get('ZfcRbac\Service\AuthorizationService');

        $permission = 'guest-permission';
        $this->assertTrue(
            $authorizationService->isGranted($permission),
            sprintf('Current identity is not granted \'%s\' permission', $permission)
        );

        $permission = 'admin-permission';
        $this->assertFalse(
            $authorizationService->isGranted($permission),
            sprintf('Current identity should not be granted \'%s\' permission', $permission)
        );
    }
}
