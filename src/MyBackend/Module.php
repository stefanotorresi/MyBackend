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
    Feature\ConsoleUsageProviderInterface
{
    public function onBootstrap(MvcEvent $event)
    {
        $eventManager = $event->getApplication()->getEventManager();

        $eventManager->attach(new Listener\Login());
        $eventManager->attach(new Listener\Route());
        $eventManager->attach(new Listener\Render());
        $eventManager->attach(new Listener\Error());
    }

    /**
     * {@inheritdoc}
     */
    public function getConsoleUsage(ConsoleAdapterInterface $console)
    {
        return [
            'user create-admin [--username=] [--email=]' => 'Create a new admin user',
            'user delete [--id=] [--username=] [--email=]' => 'Delete a user',
        ];
    }
}
