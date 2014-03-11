<?php
/**
 * @author Stefano Torresi (http://stefanotorresi.it)
 * @license See the file LICENSE.txt for copying permission.
 * ************************************************
 */

namespace MyBackend\Test\Listener;

use MyBackend\Entity\Fixture\PermissionFixture;
use MyBackend\Listener\UnauthorizedListener;
use MyBackend\Module;
use MyBackend\Options\ModuleOptions;
use PHPUnit_Framework_TestCase as TestCase;
use Zend\Console\Request as ConsoleRequest;
use Zend\EventManager\EventManager;
use Zend\Http\Request as HttpRequest;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\RouteMatch;
use Zend\Stdlib\CallbackHandler;
use ZfcRbac\Guard\AbstractGuard;
use ZfcRbac\Service\AuthorizationService;

class UnauthorizedListenerTest extends TestCase
{
    /**
     * @var AuthorizationService
     */
    protected $authorizationService;

    /**
     * @var ModuleOptions
     */
    protected $moduleOptions;

    /**
     * @var UnauthorizedListener
     */
    protected $listener;

    public function setUp()
    {
        $moduleOptions = new ModuleOptions();
        $authorizationService = $this->getMock('ZfcRbac\Service\AuthorizationService', [], [], '', false);

        $listener = new UnauthorizedListener($authorizationService);
        $listener->setModuleOptions($moduleOptions);

        $this->listener             = $listener;
        $this->moduleOptions        = $moduleOptions;
        $this->authorizationService = $authorizationService;
    }

    public function testDoesNothingIfNotInMyBackendModule()
    {
        $event = new MvcEvent();
        $this->assertNull($this->listener->onUnauthorizedError($event));
    }

    public function testDoesNothingIfNoHttpRequest()
    {
        $event = new MvcEvent();
        $event->setParam('module', new Module());
        $event->setRequest(new ConsoleRequest());
        $this->assertNull($this->listener->onUnauthorizedError($event));
    }

    public function testDoesNothingIfNoUnauthorizedError()
    {
        $event = new MvcEvent();
        $event->setParam('module', new Module());
        $event->setRequest(new HttpRequest());
        $this->assertNull($this->listener->onUnauthorizedError($event));
    }

    public function testDoesNothingIfIdentityHasNoGrantToAdminLoginNorAdminDashboard()
    {
        $event = new MvcEvent();
        $event->setParam('module', new Module());
        $event->setRequest(new HttpRequest());
        $event->setError(AbstractGuard::GUARD_UNAUTHORIZED);

        // simulate a user who is neither guest nor admin
        $this->authorizationService
            ->expects($this->exactly(2))
            ->method('isGranted')
            ->with($this->logicalOr(PermissionFixture::CAN_LOGIN_AS_ADMIN, PermissionFixture::CAN_USE_ADMIN_DASHBOARD))
            ->will($this->returnValue(false));

        $this->assertNull($this->listener->onUnauthorizedError($event));
    }

    public function testWillRedirectToAdminLoginIfGranted()
    {
        $event = new MvcEvent();
        $event->setParam('module', new Module());
        $event->setRequest(new HttpRequest());
        $event->setError(AbstractGuard::GUARD_UNAUTHORIZED);
        $event->setRouteMatch((new RouteMatch([]))->setMatchedRouteName($this->moduleOptions->getBackendRoute()));
        $router = $this->getMock('Zend\Mvc\Router\RouteStackInterface');
        $event->setRouter($router);

        // simulate guest authorization
        $this->authorizationService
            ->expects($this->exactly(2))
            ->method('isGranted')
            ->with($this->logicalOr(PermissionFixture::CAN_LOGIN_AS_ADMIN, PermissionFixture::CAN_USE_ADMIN_DASHBOARD))
            ->will($this->returnCallback(function ($value) {
                return $value === PermissionFixture::CAN_LOGIN_AS_ADMIN;
            }));

        $router->expects($this->atLeastOnce())
               ->method('assemble')
               ->with($this->anything(), ['name' => $this->moduleOptions->getBackendLoginRoute()])
               ->will($this->returnValue('login-url'));

        /** @var \Zend\Http\Response $result */
        $result = $this->listener->onUnauthorizedError($event);

        $this->assertInstanceOf('Zend\Http\Response', $result);
        $this->assertEquals($result->getHeaders()->get('location')->getFieldValue('uri'), 'login-url');
        $this->assertTrue($event->propagationIsStopped());
    }

    public function testWillRedirectToAdminDashboardIfGrantedAndAdminLoginRequested()
    {
        $event = new MvcEvent();
        $event->setParam('module', new Module());
        $event->setRequest(new HttpRequest());
        $event->setError(AbstractGuard::GUARD_UNAUTHORIZED);
        $event->setRouteMatch((new RouteMatch([]))->setMatchedRouteName($this->moduleOptions->getBackendLoginRoute()));
        $router = $this->getMock('Zend\Mvc\Router\RouteStackInterface');
        $event->setRouter($router);

        // simulate admin authorization
        $this->authorizationService
            ->expects($this->exactly(2))
            ->method('isGranted')
            ->with($this->logicalOr(PermissionFixture::CAN_LOGIN_AS_ADMIN, PermissionFixture::CAN_USE_ADMIN_DASHBOARD))
            ->will($this->returnCallback(function ($value) {
                return $value === PermissionFixture::CAN_USE_ADMIN_DASHBOARD;
            }));

        $router->expects($this->atLeastOnce())
            ->method('assemble')
            ->with($this->anything(), ['name' => $this->moduleOptions->getBackendRoute()])
            ->will($this->returnValue('dashboard-url'));

        /** @var \Zend\Http\Response $result */
        $result = $this->listener->onUnauthorizedError($event);

        $this->assertInstanceOf('Zend\Http\Response', $result);
        $this->assertEquals($result->getHeaders()->get('location')->getFieldValue('uri'), 'dashboard-url');
        $this->assertTrue($event->propagationIsStopped());
    }

    public function testAttachesToDispatchError()
    {
        $eventManager = new EventManager();
        $this->listener->attach($eventManager);

        $handlers = $eventManager->getListeners(MvcEvent::EVENT_DISPATCH_ERROR)->toArray();

        $this->assertGreaterThan(0, count($handlers));
        $callbackHandler = $handlers[0]; /** @var CallbackHandler $callbackHandler */
        $callbackArray = $callbackHandler->getCallback();
        $this->assertSame($this->listener, $callbackArray[0]);
        $this->assertEquals('onUnauthorizedError', $callbackArray[1]);
        $this->assertEquals($callbackHandler->getMetadatum('priority'), 1);
    }
}
