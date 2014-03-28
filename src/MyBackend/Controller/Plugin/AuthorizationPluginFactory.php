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

class AuthorizationPluginFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param  ServiceLocatorInterface $serviceLocator
     * @return AuthorizationPlugin
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $serviceManager = ( $serviceLocator instanceof AbstractPluginManager ) ?
            $serviceLocator->getServiceLocator() : $serviceLocator;

        return new AuthorizationPlugin($serviceManager->get('ZfcRbac\Service\AuthorizationService'));
    }
}
