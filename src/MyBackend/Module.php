<?php
/**
 * @author Stefano Torresi (http://stefanotorresi.it)
 * @license See the file LICENSE.txt for copying permission.
 * ************************************************
 */

namespace MyBackend;

use MyBase\AbstractModule;
use Zend\Console\Adapter\AdapterInterface as ConsoleAdapterInterface;
use Zend\Mvc\MvcEvent;
use Zend\ModuleManager\Feature;

class Module extends AbstractModule implements
    Feature\ConsoleUsageProviderInterface,
    Feature\ServiceProviderInterface
{
    public function onBootstrap(MvcEvent $event)
    {
        $eventManager = $event->getApplication()->getEventManager();

        $eventManager->attach(new Listener\Login());
        $eventManager->attach(new Listener\Route());
        $eventManager->attach(new Listener\Render());
        $eventManager->attach(new Listener\UnauthorizedListener());
    }

    /**
     * {@inheritdoc}
     */
    public function getConsoleUsage(ConsoleAdapterInterface $console)
    {
        return [
            'user create [--username=] [--email=] [--roles=]' => 'Create a new user',
            'user delete [--id=] [--username=] [--email=]' => 'Delete a user',
        ];
    }

    /**
     * Expected to return \Zend\ServiceManager\Config object or array to
     * seed such an object.
     *
     * @return array|\Zend\ServiceManager\Config
     */
    public function getServiceConfig()
    {
        // TODO: Implement getServiceConfig() method.
    }
}
