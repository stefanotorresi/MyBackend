<?php
/**
 *
 * @author Stefano Torresi (http://stefanotorresi.it)
 * @license See the file LICENSE.txt for copying permission.
 * ************************************************
 */

namespace MyBackend\Listener;

use LazyProperty\LazyPropertiesTrait;
use MyBackend\Module as MyBackend;
use MyBackend\Options\ModuleOptionsAwareInterface;
use MyBackend\Options\ModuleOptionsAwareTrait;
use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventManagerInterface;
use Zend\Mvc\MvcEvent;

class RouteListener extends AbstractListenerAggregate implements ModuleOptionsAwareInterface
{
    use LazyPropertiesTrait;
    use ModuleOptionsAwareTrait;

    /**
     *
     */
    public function __construct()
    {
        $this->initLazyProperties(['moduleOptions']);
    }

    /**
     * {@inheritDoc}
     */
    public function attach(EventManagerInterface $eventManager)
    {
        $this->listeners[] = $eventManager->attach(MvcEvent::EVENT_ROUTE, [$this, 'selectModule'], -1);
    }

    /**
     * @param MvcEvent $e
     */
    public function selectModule(MvcEvent $e)
    {
        $routeName      = $e->getRouteMatch()->getMatchedRouteName();
        $serviceManager = $e->getApplication()->getServiceManager();

        $parentRoute = $this->moduleOptions->getBackendRoute();

        if (strpos($routeName, $parentRoute) !== 0) {
            return;
        }

        /** @var MyBackend $module  */
        $module = $serviceManager->get('ModuleManager')->getModule('MyBackend');

        $e->setParam('module', $module);
    }
}
