<?php
/**
 * @author Stefano Torresi (http://stefanotorresi.it)
 * @license See the file LICENSE.txt for copying permission.
 * ************************************************
 */

namespace MyBackend\Controller\Plugin;

use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class LoginPluginFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return LoginPlugin
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $serviceManager = ( $serviceLocator instanceof AbstractPluginManager ) ?
            $serviceLocator->getServiceLocator() : $serviceLocator;

        return new LoginPlugin($serviceManager->get('MyBackend\Service\UserService'));
    }
}
