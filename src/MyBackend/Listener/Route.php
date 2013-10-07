<?php
/**
 *
 * @author Stefano Torresi (http://stefanotorresi.it)
 * @license See the file LICENSE.txt for copying permission.
 * ************************************************
 */

namespace MyBackend\Listener;

use Zend\Mvc\MvcEvent;

class Route
{
    /**
     * @param  MvcEvent                            $e
     * @return null|\Zend\Stdlib\ResponseInterface
     */
    public static function onRoute(MvcEvent $e)
    {
        /** @var \Zend\Mvc\Application $app  */
        $app    = $e->getTarget();
        $serviceManager = $app->getServiceManager();
        $router = $e->getRouter();
        $routeName  = $e->getRouteMatch()->getMatchedRouteName();
        $module = $serviceManager->get('ModuleManager')->getModule('MyBackend');

        $parentRoute = $module->getOption('routes.backend');
        $loginRoute = $module->getOption('routes.login');

        if ($routeName === $loginRoute || strpos($routeName, $parentRoute) !== 0) {
            return;
        }

        $rbacService = $serviceManager->get('ZfcRbac\Service\Rbac');

        if (!$rbacService->getFirewall('route')->isGranted($routeName)) {
            $url = $router->assemble(array(), array('name' => $loginRoute));

            $response = $e->getResponse();
            $response->getHeaders()->addHeaderLine('Location', $url);
            $response->setStatusCode(302);

            return $response;
        }
    }
}
