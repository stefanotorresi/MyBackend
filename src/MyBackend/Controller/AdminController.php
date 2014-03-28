<?php
/**
 *
 * @author Stefano Torresi (http://stefanotorresi.it)
 * @license See the file LICENSE.txt for copying permission.
 * ************************************************
 */

namespace MyBackend\Controller;

use LazyProperty\LazyPropertiesTrait;
use MyBackend\Entity\Fixture\PermissionFixture as Permissions;
use MyBackend\Options\ModuleOptions;
use MyBackend\Service\UserService;
use Zend\Http\Request;
use Zend\Http\Response;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use ZfcRbac\Exception\UnauthorizedException;
use ZfcRbac\Mvc\Controller\Plugin\IsGranted;

/**
 * Class AdminController
 * @package MyBackend\Controller
 * @method IsGranted isGranted()
 * @method Plugin\LoginPlugin login()
 */
class AdminController extends AbstractActionController
{
    use LazyPropertiesTrait;

    /**
     * @var ModuleOptions $moduleOptions
     */
    protected $moduleOptions;

    /**
     * @var UserService
     */
    protected $userService;

    /**
     *
     */
    public function __construct()
    {
        $this->initLazyProperties([ 'userService', 'moduleOptions' ]);
    }

    /**
     *
     */
    public function indexAction()
    {
        // @todo some dashboard stub template
        return false;
    }

    /**
     * @throws \ZfcRbac\Exception\UnauthorizedException
     * @return ViewModel|Response
     */
    public function loginAction()
    {
        if ($this->isGranted(Permissions::ADMIN_ACCESS)) {
            return $this->redirect()->toRoute($this->moduleOptions->getBackendRoute());
        }

        if (! $this->isGranted(Permissions::GUEST_ACCESS)) {
            throw new UnauthorizedException();
        }

        $data = $this->prg();

        $assertion = function (AdminController $controller) {
            return $controller->isGranted(Permissions::ADMIN_ACCESS);
        };

        return $this->login($data, $this->moduleOptions->getBackendRoute(), $assertion);
    }

    /**
     * @return mixed
     */
    public function logoutAction()
    {
        /** @var Request $request */
        $request = $this->getRequest();
        $request->getQuery()->set('redirect', $this->url()->fromRoute($this->moduleOptions->getPostLogoutRoute()));

        return $this->forward()->dispatch('zfcuser');
    }

    /**
     * @return ModuleOptions
     */
    public function getModuleOptions()
    {
        if (! $this->moduleOptions) {
            $this->moduleOptions = $this->getServiceLocator()->get('MyBackend\Options\ModuleOptions');
        }

        return $this->moduleOptions;
    }

    /**
     * @param ModuleOptions $moduleOptions
     */
    public function setModuleOptions($moduleOptions)
    {
        $this->moduleOptions = $moduleOptions;
    }

    /**
     * @return UserService
     */
    public function getUserService()
    {
        if (! $this->userService) {
            $this->userService = $this->getServiceLocator()->get('MyBackend\Service\UserService');
        }

        return $this->userService;
    }

    /**
     * @param UserService $userService
     */
    public function setUserService($userService)
    {
        $this->userService = $userService;
    }
}
