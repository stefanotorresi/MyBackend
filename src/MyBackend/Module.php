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

        $eventManager->attach(new Listener\Login());
        $eventManager->attach(new Listener\Route());
        $eventManager->attach(new Listener\Render());
        $eventManager->attach(new Listener\Error());
    }
}
