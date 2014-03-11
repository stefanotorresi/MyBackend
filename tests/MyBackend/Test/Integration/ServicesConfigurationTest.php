<?php
/**
 * @author Stefano Torresi (http://stefanotorresi.it)
 * @license See the file LICENSE.txt for copying permission.
 * ************************************************
 */

namespace MyBackend\Test\Integration;

use MyBackend\Test\Bootstrap;
use PHPUnit_Framework_TestCase as TestCase;

class ServicesConfigurationTest extends TestCase
{
    /**
     * @dataProvider servicesProvider
     * @param $locatorInstance
     * @param $name
     * @param $class
     */
    public function testServicesConfiguration($locatorInstance, $name, $class)
    {
        $serviceManager = Bootstrap::getServiceManager();

        $serviceLocator = $locatorInstance == 'ServiceManager' ?
            $serviceManager : $serviceManager->get($locatorInstance);

        $serviceManager->get('Application')->bootstrap();

        $this->assertTrue($serviceLocator->has($name));
        $this->assertInstanceOf($class, $serviceLocator->get($name));
    }

    public function servicesProvider()
    {
        return [
            ['ServiceManager', 'MyBackend\Service\UserService', 'MyBackend\Service\UserService'],
            ['ServiceManager', 'zfcuser_user_service', 'MyBackend\Service\UserService'],
            ['ServiceManager', 'MyBackend\Mapper\UserMapper', 'MyBackend\Mapper\UserMapperInterface'],
            ['ServiceManager', 'zfcuser_user_mapper', 'MyBackend\Mapper\UserMapperInterface'],
            ['ServiceManager', 'MyBackend\Mapper\RoleMapper', 'MyBackend\Mapper\RoleMapperInterface'],
            ['ServiceManager', 'MyBackend\Navigation\BackendNavigation', 'Zend\Navigation\Navigation'],
            ['ServiceManager', 'MyBackend\Navigation\BackendBreadcrumbs', 'Zend\Navigation\Navigation'],
            ['ServiceManager', 'MyBackend\Listener\UnauthorizedListener', 'MyBackend\Listener\UnauthorizedListener'],
            ['ServiceManager', 'Zend\Authentication\AuthenticationService', 'Zend\Authentication\AuthenticationService'],
            ['ControllerLoader', 'MyBackend\Controller\AdminController', 'MyBackend\Controller\AdminController'],
            ['ControllerLoader', 'MyBackend\Controller\UserConsoleController', 'MyBackend\Controller\UserConsoleController'],
        ];
    }
}
