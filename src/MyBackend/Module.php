<?php
/**
 * @author Stefano Torresi (http://stefanotorresi.it)
 * @license See the file LICENSE.txt for copying permission.
 * ************************************************
 */

namespace MyBackend;

use MyBase\AbstractModule;
use Zend\Mvc\MvcEvent;

class Module extends AbstractModule
{
    public function onBootstrap(MvcEvent $event)
    {
        $eventManager = $event->getApplication()->getEventManager();

        $routeListener = new Listener\Route();
        $routeListener->attach($eventManager);

        $renderListener = new Listener\Render();
        $renderListener->attach($eventManager);

        $eventManager->attach(MvcEvent::EVENT_DISPATCH, array(__NAMESPACE__ . '\Listener\Login', 'preDispatch'), 999);
    }
}
