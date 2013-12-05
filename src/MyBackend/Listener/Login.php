<?php
/**
 *
 * @author Stefano Torresi (http://stefanotorresi.it)
 * @license See the file LICENSE.txt for copying permission.
 * ************************************************
 */

namespace MyBackend\Listener;

use MyBackend\Options\ModuleOptions;
use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventManagerInterface;
use Zend\Http\Request as HttpRequest;
use Zend\Mvc\MvcEvent;
use ZfcUser\Controller\UserController;

class Login extends AbstractListenerAggregate
{
    /**
     * {@inheritdoc}
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_DISPATCH, [$this, 'preDispatch'], 100);
    }

    /**
     * @param  MvcEvent                            $e
     * @return null|\Zend\Stdlib\ResponseInterface
     */
    public function preDispatch(MvcEvent $e)
    {
        $router     = $e->getRouter();
        $routeName  = $e->getRouteMatch()->getMatchedRouteName();

        if ($routeName !== UserController::ROUTE_LOGIN) {
            return;
        }

        $request = $e->getRequest();

        if (! $request instanceof HttpRequest) {
            return;
        }

        if ($request->isPost()) {
            $request->getQuery()->set('redirect', $request->getPost('redirect'));

            return;
        }

        /** @var ModuleOptions $options  */
        $options = $e->getApplication()->getServiceManager()->get('MyBackend\Options\ModuleOptions');

        $adminUrl = $router->assemble([], ['name' => $options->getBackendRoute()]);

        if ($request->getQuery('redirect') === $adminUrl || $options->getDisableFrontendLogin()) {
            $url = $router->assemble([], ['name' => $options->getBackendLoginRoute()]);

            $response = $e->getResponse();
            $response->getHeaders()->addHeaderLine('Location', $url);
            $response->setStatusCode(302);

            return $response;
        }
    }
}
