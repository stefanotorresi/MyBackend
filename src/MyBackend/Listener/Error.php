<?php
/**
 * @author Stefano Torresi (http://stefanotorresi.it)
 * @license See the file LICENSE.txt for copying permission.
 * ************************************************
 */

namespace MyBackend\Listener;

use MyBackend\Module as MyBackend;
use MyBackend\Options\ModuleOptions;
use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventManagerInterface;
use Zend\Http\Response as HttpResponse;
use Zend\Mvc\MvcEvent;
use ZfcRbac\Exception\UnauthorizedExceptionInterface;
use ZfcRbac\Service\AuthorizationService;

class Error extends AbstractListenerAggregate
{
    /**
     * {@inheritdoc}
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_DISPATCH_ERROR, [$this, 'onError']);
    }

    public function onError(MvcEvent $event)
    {
        $module = $event->getParam('module');

        if (! $module instanceof MyBackend) {
            return;
        }

        // Do nothing if no error or if response is not HTTP response
        if (!($exception = $event->getParam('exception') instanceof UnauthorizedExceptionInterface)
            || ($result = $event->getResult() instanceof HttpResponse)
            || !($response = $event->getResponse() instanceof HttpResponse)
        ) {
            return;
        }

        $serviceManager = $event->getApplication()->getServiceManager();
        $routeName      = $event->getRouteMatch()->getMatchedRouteName();

        /** @var AuthorizationService $authorizationService */
        $authorizationService = $serviceManager->get('ZfcRbac\Service\AuthorizationService');
        $adminGranted = $authorizationService->isGranted('admin');

        /** @var ModuleOptions $options  */
        $options = $serviceManager->get('MyBackend\Options\ModuleOptions');
        $loginRoute = $options->getBackendLoginRoute();
        $backendRoute = $options->getBackendRoute();

        if ($routeName === $loginRoute && ! $adminGranted) {
            return;
        }

        $router = $event->getRouter();

        if ($routeName === $loginRoute && $adminGranted) {
            $url = $router->assemble([], ['name' => $backendRoute]);
        } else {
            $url = $router->assemble([], ['name' => $loginRoute]);
        }

        $response = $event->getResponse();
        $response->getHeaders()->addHeaderLine('Location', $url);
        $response->setStatusCode(302);

        return $response;
    }
}
