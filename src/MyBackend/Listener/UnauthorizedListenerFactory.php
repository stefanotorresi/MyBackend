<?php
/**
 * @author Stefano Torresi (http://stefanotorresi.it)
 * @license See the file LICENSE.txt for copying permission.
 * ************************************************
 */

namespace MyBackend\Listener;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class UnauthorizedListenerFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param  ServiceLocatorInterface $serviceLocator
     * @return UnauthorizedListener
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new UnauthorizedListener(
            $serviceLocator->get('MyBackend\Options\ModuleOptions'),
            $serviceLocator->get('ZfcRbac\Service\AuthorizationService')
        );
    }
}
