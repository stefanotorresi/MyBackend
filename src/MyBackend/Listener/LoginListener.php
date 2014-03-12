<?php
/**
 *
 * @author Stefano Torresi (http://stefanotorresi.it)
 * @license See the file LICENSE.txt for copying permission.
 * ************************************************
 */

namespace MyBackend\Listener;

use LazyProperty\LazyPropertiesTrait;
use MyBackend\Options\ModuleOptionsAwareInterface;
use MyBackend\Options\ModuleOptionsAwareTrait;
use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventManagerInterface;
use Zend\Http\Request as HttpRequest;
use Zend\Http\Response as HttpResponse;
use Zend\Mvc\MvcEvent;
use ZfcUser\Controller\UserController;

class LoginListener extends AbstractListenerAggregate implements ModuleOptionsAwareInterface
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
     * {@inheritdoc}
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_DISPATCH, [$this, 'preDispatch'], 100);
    }

    /**
     * @param  MvcEvent          $e
     * @return HttpResponse|null
     */
    public function preDispatch(MvcEvent $e)
    {
        $request = $e->getRequest();

        if (! $request instanceof HttpRequest) {
            return;
        }

        $router     = $e->getRouter();
        $routeName  = $e->getRouteMatch()->getMatchedRouteName();

        if ($routeName !== UserController::ROUTE_LOGIN) {
            return;
        }

        if ($request->isPost()) {
            $request->getQuery()->set('redirect', $request->getPost('redirect'));

            return;
        }

        $adminUrl = $router->assemble([], ['name' => $this->moduleOptions->getBackendRoute()]);

        if ($request->getQuery('redirect') === $adminUrl || $this->moduleOptions->getDisableFrontendLogin()) {
            $url = $router->assemble([], ['name' => $this->moduleOptions->getBackendLoginRoute()]);

            $response = $e->getResponse();
            $response->getHeaders()->addHeaderLine('Location', $url);
            $response->setStatusCode(302);

            return $response;
        }
    }
}
