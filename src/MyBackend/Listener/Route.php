<?php
/**
 *
 * @author Stefano Torresi (http://stefanotorresi.it)
 * @license See the file LICENSE.txt for copying permission.
 * ************************************************
 */

namespace MyBackend\Listener;

use MyBackend\Module as MyBackend;
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
        $this->listeners[] = $eventManager->attach(MvcEvent::EVENT_ROUTE, [$this, 'rbacCheck'], -999);
    }

    public function selectModule(MvcEvent $e)
    {
        $routeName = $e->getRouteMatch()->getMatchedRouteName();
        /** @var \MyBackend\Module $module  */
        $module = $e->getApplication()->getServiceManager()->get('ModuleManager')->getModule('MyBackend');
        $parentRoute = $module->getOption('routes.backend');

        if (strpos($routeName, $parentRoute) !== 0) {
            return;
        }

        $e->setParam('module', $module);

        return $e;
    }

    /**
     * @param  MvcEvent                            $e
     * @return null|\Zend\Stdlib\ResponseInterface
     */
    public static function rbacCheck(MvcEvent $e)
    {
        $serviceManager = $e->getApplication()->getServiceManager();
        $router = $e->getRouter();
        $routeName  = $e->getRouteMatch()->getMatchedRouteName();
        $module = $e->getParam('module');

        if (! $module instanceof MyBackend) {
            return;
        }

        $loginRoute = $module->getOption('routes.login');

        if ($routeName === $loginRoute) {
            return;
        }

        $rbacService = $serviceManager->get('ZfcRbac\Service\Rbac');

        if (! $rbacService->getFirewall('route')->isGranted($routeName)) {
            $url = $router->assemble([], ['name' => $loginRoute]);

            $response = $e->getResponse();
            $response->getHeaders()->addHeaderLine('Location', $url);
            $response->setStatusCode(302);

            return $response;
        }
    }
}
