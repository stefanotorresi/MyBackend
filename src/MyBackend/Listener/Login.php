<?php
/**
 *
 * @author Stefano Torresi (http://stefanotorresi.it)
 * @license See the file LICENSE.txt for copying permission.
 * ************************************************
 */

namespace MyBackend\Listener;

use Zend\Http\Request;
use Zend\Mvc\MvcEvent;
use ZfcUser\Controller\UserController;

class Login
{
    /**
     * @param  MvcEvent                            $e
     * @return null|\Zend\Stdlib\ResponseInterface
     */
    public static function preDispatch(MvcEvent $e)
    {
        /** @var \Zend\Mvc\Application $app  */
        $app    = $e->getTarget();
        $serviceManager = $app->getServiceManager();
        $router = $e->getRouter();
        $routeName  = $e->getRouteMatch()->getMatchedRouteName();

        /** @var \MyBackend\Module $module  */
        $module = $serviceManager->get('ModuleManager')->getModule('MyBackend');

        if ($routeName !== UserController::ROUTE_LOGIN) {
            return;
        }

        $request = $e->getRequest();

        if (! $request instanceof Request) {
            return;
        }

        if ($request->isPost()) {
            $request->getQuery()->set('redirect', $request->getPost('redirect'));

            return;
        }

        $parentRoute            = $module->getOption('routes.backend');
        $loginRoute             = $module->getOption('routes.login');
        $disableFrontendLogin   = $module->getOption('disable_frontend_login', false);

        $adminUrl = $router->assemble([], ['name' => $parentRoute]);

        if ($request->getQuery('redirect') === $adminUrl || $disableFrontendLogin) {
            $url = $router->assemble([], ['name' => $loginRoute]);

            $response = $e->getResponse();
            $response->getHeaders()->addHeaderLine('Location', $url);
            $response->setStatusCode(302);

            return $response;
        }
    }
}
