<?php
/**
 *
 * @author Stefano Torresi (http://stefanotorresi.it)
 * @license See the file LICENSE.txt for copying permission.
 * ************************************************
 */

namespace MyBackend\Listener;

use MyBackend\Module as MyBackend;
use MyBackend\Options\ModuleOptions;
use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventManagerInterface;
use Zend\Mvc\MvcEvent;

class Route extends AbstractListenerAggregate
{
    /**
     * {@inheritDoc}
     */
    public function attach(EventManagerInterface $eventManager)
    {
        $this->listeners[] = $eventManager->attach(MvcEvent::EVENT_ROUTE, [$this, 'selectModule'], -1);
    }

    public function selectModule(MvcEvent $e)
    {
        $routeName      = $e->getRouteMatch()->getMatchedRouteName();
        $serviceManager = $e->getApplication()->getServiceManager();

        /** @var ModuleOptions $options  */
        $options = $serviceManager->get('MyBackend\Options\ModuleOptions');

        $parentRoute = $options->getBackendRoute();

        if (strpos($routeName, $parentRoute) !== 0) {
            return;
        }

        /** @var MyBackend $module  */
        $module = $serviceManager->get('ModuleManager')->getModule('MyBackend');

        $e->setParam('module', $module);
    }
}
