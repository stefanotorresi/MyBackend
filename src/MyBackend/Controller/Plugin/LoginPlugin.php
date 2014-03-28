<?php
/**
 * @author Stefano Torresi (http://stefanotorresi.it)
 * @license See the file LICENSE.txt for copying permission.
 * ************************************************
 */

namespace MyBackend\Controller\Plugin;

use MyBackend\Service\UserServiceAwareInterface;
use MyBackend\Service\UserServiceInterface;
use Zend\Authentication\AuthenticationService;
use Zend\Http\Request;
use Zend\Http\Response;
use Zend\Mvc\Controller\AbstractController;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\Mvc\Controller\Plugin\Params;
use Zend\Mvc\Controller\Plugin\Redirect;
use Zend\Mvc\Exception\InvalidArgumentException;
use Zend\Stdlib\DispatchableInterface as Dispatchable;
use Zend\Stdlib\Parameters;
use Zend\View\Model\ViewModel;
use ZfcUser\Authentication\Adapter\AdapterChain;
use ZfcUser\Controller\Plugin\ZfcUserAuthentication;

class LoginPlugin extends AbstractPlugin implements UserServiceAwareInterface
{
    /**
     * @var UserServiceInterface
     */
    protected $userService;

    /**
     * @param UserServiceInterface $userService
     */
    public function __construct(UserServiceInterface $userService)
    {
        $this->setUserService($userService);
    }

    /**
     * @param $data
     * @param $successRoute
     * @return Response|ViewModel
     */
    public function __invoke($data, $successRoute, callable $assertion = null)
    {
        // $data may come frome PRG plugin
        if ($data instanceof Response) {
            return $data;
        }

        $userService = $this->getUserService();
        $form        = $userService->getLoginForm();

        $viewModel = new ViewModel([
            'loginForm'  => $form,
        ]);

        if ($data === false) {
            return $viewModel;
        }

        $response = $this->controller->getResponse(); /** @var Response $response */
        $form->setData($data);

        if (! $form->isValid()) {
            $response->setStatusCode(400);

            return $viewModel;
        }

        $authPlugin = $this->controller->plugin('zfcUserAuthentication'); /** @var ZfcUserAuthentication $authPlugin */

        $authAdapter = $authPlugin->getAuthAdapter(); /** @var AdapterChain $authAdapter */
        $authService = $authPlugin->getAuthService(); /** @var AuthenticationService $authService */

        $authAdapter->resetAdapters();
        $authService->clearIdentity();

        $request  = $this->controller->getRequest(); /** @var Request $request */

        if ($request->isGet() && ! empty($data)) {
            // since ZfcUser auth adapter directly inspects the request, we have to convert the PRG back to a POST
            if (! $data instanceof Parameters) {
                $data = new Parameters($data);
            }
            $request->setPost($data);
        }

        // triggers 'authenticate.pre' and 'authenticate' events
        $result = $authAdapter->prepareForAuthentication($request);

        // return early if any listener returns a Response
        if ($result instanceof Response) {
            return $result;
        }

        $authResult = $authService->authenticate($authAdapter);

        $assertion = $assertion ? call_user_func($assertion, $this->controller, $authResult) : true;

        if (! $authResult->isValid() || $assertion === false) {
            $response->setStatusCode(401);
            $viewModel->setVariable('authFailed', true);
            $authAdapter->resetAdapters();
            $authService->clearIdentity();

            return $viewModel;
        }

        $paramsPlugin   = $this->controller->plugin('params'); /** @var Params $paramsPlugin */
        $redirectPlugin = $this->controller->plugin('redirect'); /** @var Redirect $redirectPlugin */

        $redirect = $paramsPlugin->fromPost('redirect', $paramsPlugin->fromQuery('redirect'));

        if ($userService->getOptions()->getUseRedirectParameterIfPresent() && $redirect) {
            return $redirectPlugin->toUrl($redirect);
        }

        return $redirectPlugin->toRoute($successRoute);
    }

    /**
     * @param  Dispatchable                                 $controller
     * @throws \Zend\Mvc\Exception\InvalidArgumentException
     */
    public function setController(Dispatchable $controller)
    {
        if (! $controller instanceof AbstractController) {
            throw new InvalidArgumentException(
                sprintf('%s may only be invoked by controllers extending AbstractController', __CLASS__)
            );
        }

        parent::setController($controller);
    }

    /**
     * @return UserServiceInterface
     */
    public function getUserService()
    {
        return $this->userService;
    }

    /**
     * @param UserServiceInterface $userService
     */
    public function setUserService(UserServiceInterface $userService)
    {
        $this->userService = $userService;
    }
}
