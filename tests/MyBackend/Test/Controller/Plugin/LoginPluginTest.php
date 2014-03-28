<?php
/**
 * @author Stefano Torresi (http://stefanotorresi.it)
 * @license See the file LICENSE.txt for copying permission.
 * ************************************************
 */

namespace MyBackend\Test\Controller\Plugin;

use MyBackend\Controller\Plugin\LoginPlugin;
use MyBackend\Service\UserServiceInterface;
use PHPUnit_Framework_TestCase as TestCase;
use Zend\Authentication\Result;
use Zend\Http\Response;
use Zend\Mvc\Controller\AbstractController;
use Zend\Stdlib\Parameters;
use Zend\View\Model\ViewModel;
use ZfcUser\Controller\Plugin\ZfcUserAuthentication;
use ZfcUser\Options\ModuleOptions;

class LoginPluginTest extends TestCase
{
    /**
     * @var LoginPlugin
     */
    protected $plugin;

    /**
     * @var UserServiceInterface
     */
    protected $userService;

    /**
     * @var AbstractController
     */
    protected $controller;

    /**
     * @var ModuleOptions
     */
    protected $userServiceOptions;

    public function setUp()
    {
        $this->userService = $this->getMock('MyBackend\Service\UserServiceInterface');

        $this->userServiceOptions = new ModuleOptions();

        $this->userService->expects($this->any())
                          ->method('getOptions')
                          ->will($this->returnValue($this->userServiceOptions));

        $this->controller = new TestAsset\Controller;

        // let's make assembled routes just route names
        $router = $this->getMock('Zend\Mvc\Router\RouteStackInterface');
        $router->expects($this->any())
               ->method('assemble')
               ->will($this->returnArgument(1));

        $event = $this->controller->getEvent();
        $event->setRouter($router);

        $this->plugin = new LoginPlugin($this->userService);
        $this->plugin->setController($this->controller);
    }

    public function testControllerSetterOnlyAcceptsAbstractControllers()
    {
        $dispatchable = $this->getMock('Zend\Stdlib\DispatchableInterface');

        $this->setExpectedException('Zend\Mvc\Exception\InvalidArgumentException');

        $this->plugin->setController($dispatchable);
    }

    public function testReturnsImmediatlyIfDataIsAResponse()
    {
        $data = new Response();

        $this->assertSame($data, $this->plugin->__invoke($data, null));
    }

    /**
     * @dataProvider emptyDataProvider
     */
    public function testLoginActionReturnsViewModelWithLoginFormWhenNoDataIsSubmitted($data, $successRoute)
    {
        $loginForm = $this->getMock('ZfcUser\Form\Login', [], [], '', false);
        $this->userService->expects($this->atLeastOnce())
                          ->method('getLoginForm')
                          ->will($this->returnValue($loginForm));

        $result = $this->plugin->__invoke($data, $successRoute);
        $this->assertInstanceOf('Zend\View\Model\ViewModel', $result);
        $this->assertSame($loginForm, $result->loginForm);
    }

    public function emptyDataProvider()
    {
        return [
            [ null, null ],
            [ false, ''],
            [ '', false],
            [ [], null],
            [ new Parameters(), null],
        ];
    }

    /**
     * @dataProvider invalidAuthDataProvider
     */
    public function testLoginActionWithInvalidAuth($data)
    {
        $this->prepareAuthenticationPlugin(new Result(Result::FAILURE, null));

        $loginForm = $this->getMock('ZfcUser\Form\Login', [], [], '', false);
        $loginForm->expects($this->once())
                  ->method('isValid')
                  ->will($this->returnValue(true));

        $this->userService->expects($this->atLeastOnce())
                          ->method('getLoginForm')
                          ->will($this->returnValue($loginForm));

        $result = $this->plugin->__invoke($data, null);
        /** @var ViewModel $result */

        $this->assertEquals(401, $this->controller->getResponse()->getStatusCode());
        $this->assertInstanceOf('Zend\View\Model\ViewModel', $result);
        $this->assertTrue($result->authFailed);
    }

    public function invalidAuthDataProvider()
    {
        return [
            [ new Parameters() ],
            [ new Parameters([ 'some', 'data' ]) ],
            [ [] ],
            [ [ 'some', 'data' ] ],
        ];
    }

    public function testLoginActionWillReturnEarlyIfAuthenticationReturnsResponse()
    {
        $this->prepareAuthenticationPlugin(null);
        $response = new Response();
        $this->controller->zfcUserAuthentication()
                         ->getAuthAdapter()
                         ->expects($this->atLeastOnce())
                         ->method('prepareForAuthentication')
                         ->will($this->returnValue($response));

        $loginForm = $this->getMock('ZfcUser\Form\Login', [], [], '', false);
        $loginForm->expects($this->once())
                  ->method('isValid')
                  ->will($this->returnValue(true));

        $this->userService->expects($this->atLeastOnce())
                          ->method('getLoginForm')
                          ->will($this->returnValue($loginForm));

        $this->assertSame($response, $this->plugin->__invoke([], null));
    }

    public function testLoginActionWithValidAuth()
    {
        $this->prepareAuthenticationPlugin(new Result(Result::SUCCESS, null));

        $loginForm = $this->getMock('ZfcUser\Form\Login', [], [], '', false);
        $loginForm->expects($this->once())
                  ->method('isValid')
                  ->will($this->returnValue(true));

        $this->userService->expects($this->atLeastOnce())
                          ->method('getLoginForm')
                          ->will($this->returnValue($loginForm));

        $this->controller->getEvent()->setResponse(new Response());

        $successRoute = 'success-route';

        $this->userServiceOptions->setUseRedirectParameterIfPresent(false);

        $result = $this->plugin->__invoke([], $successRoute);
        /** @var Response $result */

        $this->assertInstanceOf('Zend\Http\Response', $result);
        $this->assertTrue($result->isRedirect());
        $this->assertEquals(
            $successRoute,
            $result->getHeaders()->get('Location')->getFieldValue()
        );
    }

    public function testAssertion()
    {
        $authResult = new Result(Result::SUCCESS, null);
        $this->prepareAuthenticationPlugin($authResult);

        $loginForm = $this->getMock('ZfcUser\Form\Login', [], [], '', false);
        $loginForm->expects($this->atLeastOnce())
                  ->method('isValid')
                  ->will($this->returnValue(true));

        $this->userService->expects($this->atLeastOnce())
                          ->method('getLoginForm')
                          ->will($this->returnValue($loginForm));

        $this->controller->getEvent()->setResponse(new Response());

        $this->userServiceOptions->setUseRedirectParameterIfPresent(false);

        $result = $this->plugin->__invoke(
            [],
            null,
            function ($passedController, $passedAuthResult) use ($authResult) {
                $this->assertSame($this->controller, $passedController);
                $this->assertSame($authResult, $passedAuthResult);

                return false;
            }
        );
        /** @var ViewModel $result */

        $this->assertEquals(401, $this->controller->getResponse()->getStatusCode());
        $this->assertInstanceOf('Zend\View\Model\ViewModel', $result);
        $this->assertTrue($result->authFailed);

        $result = $this->plugin->__invoke([], 'some-route', function () {
            return true;
        });
        /** @var Response $result */

        $this->assertNotEquals(401, $result->getStatusCode());
    }

    public function testPostLoginRedirectOption()
    {
        $this->prepareAuthenticationPlugin(new Result(Result::SUCCESS, null));

        $loginForm = $this->getMock('ZfcUser\Form\Login', [], [], '', false);
        $loginForm->expects($this->once())
            ->method('isValid')
            ->will($this->returnValue(true));

        $this->userService->expects($this->atLeastOnce())
            ->method('getLoginForm')
            ->will($this->returnValue($loginForm));

        $this->controller->getEvent()->setResponse(new Response());

        $successRoute = 'success-route';
        $redirectParam = 'success-url';

        $this->controller->getRequest()->getPost()->set('redirect', $redirectParam);

        $result = $this->plugin->__invoke([], $successRoute);
        /** @var Response $result */

        $this->assertInstanceOf('Zend\Http\Response', $result);
        $this->assertTrue($result->isRedirect());
        $this->assertNotEquals(
            $successRoute,
            $result->getHeaders()->get('Location')->getFieldValue()
        );

        $this->assertEquals(
            $redirectParam,
            $result->getHeaders()->get('Location')->getFieldValue()
        );
    }

    protected function prepareAuthenticationPlugin($result = null)
    {
        $authPlugin  = new ZfcUserAuthentication;
        $authAdapter = $this->getMock('ZfcUser\Authentication\Adapter\AdapterChain');
        $authService = $this->getMock('Zend\Authentication\AuthenticationService');

        if ($result) {
            $authService->expects($this->atLeastOnce())
                        ->method('authenticate')
                        ->with($authAdapter)
                        ->will($this->returnValue($result));
        }

        $authService->expects($this->atLeastOnce())
                    ->method('clearIdentity');
        $authAdapter->expects($this->atLeastOnce())
                    ->method('resetAdapters');

        $authPlugin->setController($this->controller);
        $authPlugin->setAuthAdapter($authAdapter);
        $authPlugin->setAuthService($authService);

        $this->controller->getPluginManager()->setService('zfcUserAuthentication', $authPlugin);
    }

    protected function prepareAuthorizationPlugin($permission, $result, $context = null)
    {
        $authorizationPlugin = $this->getMock('MyBackend\Controller\Plugin\AuthorizationPlugin', [], [], '', null);
        $authorizationPlugin->expects($this->atLeastOnce())
                            ->method('__invoke')
                            ->with($permission, $context)
                            ->will($this->returnValue($result));

        $this->controller->getPluginManager()->setService('isGranted', $authorizationPlugin);
    }
}
