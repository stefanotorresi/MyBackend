<?php
/**
 * @author Stefano Torresi (http://stefanotorresi.it)
 * @license See the file LICENSE.txt for copying permission.
 * ************************************************
 */

namespace MyBackend;

use Zend\Mvc\MvcEvent;
use ZfcBase\Module\AbstractModule;
use Zend\ModuleManager\Feature;
use Zend\ModuleManager\ModuleManager;
use Zend\Mvc\ApplicationInterface;

class Module extends AbstractModule implements
    Feature\ConfigProviderInterface
{
    public function bootstrap(ModuleManager $moduleManager, ApplicationInterface $app)
    {
        $eventManager = $app->getEventManager();

        $routeListener = new Listener\Route();
        $routeListener->attach($eventManager);

        $renderListener = new Listener\Render();
        $renderListener->attach($eventManager);

        $eventManager->attach(MvcEvent::EVENT_DISPATCH, array(__NAMESPACE__ . '\Listener\Login', 'preDispatch'), 999);
    }

    public function getDir()
    {
        return __DIR__ . '/../..';
    }

    public function getNamespace()
    {
        return __NAMESPACE__;
    }
}
