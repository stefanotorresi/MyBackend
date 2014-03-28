<?php
/**
 * @author Stefano Torresi (http://stefanotorresi.it)
 * @license See the file LICENSE.txt for copying permission.
 * ************************************************
 */

namespace MyBackend\Listener;

use LazyProperty\LazyPropertiesTrait;
use MyBackend\Entity\Fixture\PermissionFixture;
use MyBackend\Module as MyBackend;
use MyBackend\Options\ModuleOptionsAwareInterface;
use MyBackend\Options\ModuleOptionsAwareTrait;
use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventManagerInterface;
use Zend\Http\Request as HttpRequest;
use Zend\Http\Response as HttpResponse;
use Zend\Mvc\MvcEvent;
use ZfcRbac\Guard\AbstractGuard;
use ZfcRbac\Service\AuthorizationService;

class UnauthorizedListener extends AbstractListenerAggregate implements ModuleOptionsAwareInterface
{
    use LazyPropertiesTrait;
    use ModuleOptionsAwareTrait;

    /**
     * @var AuthorizationService
     */
    protected $authorizationService;

    /**
     * @param AuthorizationService $authorizationService
     */
    public function __construct(AuthorizationService $authorizationService)
    {
        $this->initLazyProperties(['moduleOptions']);
        $this->authorizationService = $authorizationService;
    }

    /**
     * {@inheritdoc}
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(
            MvcEvent::EVENT_DISPATCH_ERROR,
            [$this, 'onUnauthorizedError']
        );
    }

    /**
     * Handles unauthorized dispatch errors inside the admin backend
     *
     * @param  MvcEvent                       $event
     * @return \Zend\Stdlib\ResponseInterface
     */
    public function onUnauthorizedError(MvcEvent $event)
    {
        // Only handle requests handled this module, determined by RouteListener
        $module = $event->getParam('module');
        if (! $module instanceof MyBackend) {
            return;
        }

        // Ignore non HTTP requests
        if (! $event->getRequest() instanceof HttpRequest) {
            return;
        }

        // Do nothing if no unauthorized error
        if ($event->getError() !== AbstractGuard::GUARD_UNAUTHORIZED) {
            return;
        }

        $hasAdminAccess     = $this->authorizationService->isGranted(PermissionFixture::ADMIN_ACCESS);
        $hasGuestAccess     = $this->authorizationService->isGranted(PermissionFixture::GUEST_ACCESS);
        $backendLoginRoute  = $this->moduleOptions->getBackendLoginRoute();
        $backendRoute       = $this->moduleOptions->getBackendRoute();

        // unauthorized admin backend request, bail out and let zfcrbac handle it
        if (! $hasAdminAccess && ! $hasGuestAccess) {
            return;
        }

        $router    = $event->getRouter();
        $routeName = $event->getRouteMatch()->getMatchedRouteName();

        // redirect backend login page to dashboard if admin is already logged in, otherwise redirect to backend login
        $url = $router->assemble([], [
            'name' => ($routeName === $backendLoginRoute && $hasAdminAccess) ? $backendRoute : $backendLoginRoute
        ]);

        $event->stopPropagation(true);
        $response = $event->getResponse() ?: new HttpResponse();
        $response->getHeaders()->addHeaderLine('Location', $url);
        $response->setStatusCode(302);

        return $response;
    }
}
