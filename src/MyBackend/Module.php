<?php
/**
 * @author Stefano Torresi (http://stefanotorresi.it)
 * @license See the file LICENSE.txt for copying permission.
 * ************************************************
 */

namespace MyBackend;

use Doctrine\ORM\Mapping\Driver\XmlDriver;
use MyBase\AbstractModule;
use Zend\Console\Adapter\AdapterInterface as ConsoleAdapterInterface;
use Zend\Mvc\MvcEvent;
use Zend\ModuleManager\Feature;

class Module extends AbstractModule implements
    Feature\ConsoleUsageProviderInterface
{
    public function onBootstrap(MvcEvent $event)
    {
        $application    = $event->getApplication();
        $eventManager   = $application->getEventManager();
        $serviceManager = $application->getServiceManager();

        $eventManager->attach($serviceManager->get('MyBackend\Listener\LoginListener'));
        $eventManager->attach($serviceManager->get('MyBackend\Listener\RenderListener'));
        $eventManager->attach($serviceManager->get('MyBackend\Listener\RouteListener'));
        $eventManager->attach($serviceManager->get('MyBackend\Listener\UnauthorizedListener'));

        /** @var Options\ModuleOptions $options */
        $options = $serviceManager->get('MyBackend\Options\ModuleOptions');

        // default User entity mapping is opt-out, as it will be overridden in most cases
        if ($options->getLoadDefaultUserMapping()) {
            $doctrineDriverChain = $serviceManager->get('doctrine.driver.orm_default');
            $doctrineDriverChain->addDriver(
                new XmlDriver(__DIR__ . '/../../config/mappings', '.dcm.optional.xml'),
                'MyBackend\Entity\User'
            );
        }
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
}
