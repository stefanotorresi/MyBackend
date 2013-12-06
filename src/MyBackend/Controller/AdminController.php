<?php
/**
 *
 * @author Stefano Torresi (http://stefanotorresi.it)
 * @license See the file LICENSE.txt for copying permission.
 * ************************************************
 */

namespace MyBackend\Controller;

use MyBackend\Options\ModuleOptions;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\ViewModel;

class AdminController extends AbstractActionController
{
    /**
     * @var ModuleOptions $moduleOptions
     */
    protected $moduleOptions;

    public function indexAction()
    {
        return false;
    }

    public function loginAction()
    {
        $backendRoute = $this->getModuleOptions()->getBackendRoute();

        /** @var \ZfcUser\Options\ModuleOptions $zfcUserOptions  */
        $zfcUserOptions = $this->getServiceLocator()->get('zfcuser_module_options');
        $zfcUserOptions->setLoginRedirectRoute($backendRoute);

        $controller = $this;
        $this->getEvent()->getApplication()->getEventManager()->attach(
            MvcEvent::EVENT_RENDER,
            function (MvcEvent $e) use ($controller, $backendRoute) {
                foreach ($e->getViewModel()->getIterator() as $child) {
                    /** @var ViewModel $child  */
                    if ($child->captureTo() === 'content') {
                        $child->setTemplate('my-backend/login');
                        $child->setVariable('redirect', $controller->url()->fromRoute($backendRoute));
                    }
                }
            },
            1000
        );

        return $this->forward()->dispatch('zfcuser');
    }

    /**
     * @return \MyBackend\Options\ModuleOptions
     */
    public function getModuleOptions()
    {
        if (! $this->moduleOptions) {
            $this->moduleOptions = $this->getServiceLocator()->get('MyBackend\Options\ModuleOptions');
        }

        return $this->moduleOptions;
    }

    /**
     * @param \MyBackend\Options\ModuleOptions $moduleOptions
     */
    public function setModuleOptions($moduleOptions)
    {
        $this->moduleOptions = $moduleOptions;
    }
}
