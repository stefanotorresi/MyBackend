<?php
/**
 * @author Stefano Torresi (http://stefanotorresi.it)
 * @license See the file LICENSE.txt for copying permission.
 * ************************************************
 */

namespace MyBackend\Test\Options;

use MyBackend\Options\ModuleOptionsAwareInitializer;
use MyProject\Proxies\__CG__\OtherProject\Proxies\__CG__\stdClass;
use PHPUnit_Framework_TestCase as TestCase;
use MyBackend\Options\ModuleOptions;

class ModuleOptionsAwareInitializerTest extends TestCase
{
    public function testInitialize()
    {
        $instance = $this->getMock('MyBackend\Options\ModuleOptionsAwareInterface');

        $serviceLocator = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');
        $moduleOptions  = new ModuleOptions();

        $serviceLocator->expects($this->atLeastOnce())
            ->method('get')
            ->with('MyBackend\Options\ModuleOptions')
            ->will($this->returnValue($moduleOptions));

        $instance->expects($this->atLeastOnce())
            ->method('setModuleOptions')
            ->with($moduleOptions);

        $initializer = new ModuleOptionsAwareInitializer();
        $initializedInstance = $initializer->initialize($instance, $serviceLocator);

        $this->assertSame($instance, $initializedInstance);
    }

    public function testSkip()
    {
        $instance = new stdClass();
        $serviceLocator = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');

        $initializer = new ModuleOptionsAwareInitializer();
        $this->assertFalse($initializer->initialize($instance, $serviceLocator));
    }
}
