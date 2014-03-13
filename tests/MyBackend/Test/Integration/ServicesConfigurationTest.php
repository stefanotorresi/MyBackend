<?php
/**
 * @author Stefano Torresi (http://stefanotorresi.it)
 * @license See the file LICENSE.txt for copying permission.
 * ************************************************
 */

namespace MyBackend\Test\Integration;

use MyBackend\Options\ModuleOptionsAwareInterface;
use MyBackend\Test\Bootstrap;
use PHPUnit_Framework_TestCase as TestCase;

class ServicesConfigurationTest extends TestCase
{
    public function setUp()
    {
        $this->serviceManager = Bootstrap::getServiceManager();
    }

    /**
     * @dataProvider servicesProvider
     * @param $locatorInstance
     * @param $name
     * @param $class
     */
    public function testServicesConfiguration($locatorInstance, $name, $class)
    {

        $serviceLocator = $locatorInstance == 'ServiceManager' ?
            $this->serviceManager : $this->serviceManager->get($locatorInstance);

        $this->serviceManager->get('Application')->bootstrap();

        $this->assertTrue($serviceLocator->has($name));
        $service = $serviceLocator->get($name);
        $this->assertInstanceOf($class, $service);

        // ensure that MyBackend\Options\ModuleOptionsAwareInitializer is registered
        if ($service instanceof ModuleOptionsAwareInterface) {
            $this->assertSame($this->serviceManager->get('MyBackend\Options\ModuleOptions'), $service->getModuleOptions());
        }
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
            ['ServiceManager', 'MyBackend\Options\ModuleOptions', 'MyBackend\Options\ModuleOptions'],
            ['ControllerLoader', 'MyBackend\Controller\AdminController', 'MyBackend\Controller\AdminController'],
            ['ControllerLoader', 'MyBackend\Controller\UserConsoleController', 'MyBackend\Controller\UserConsoleController'],
        ];
    }
}
