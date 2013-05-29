<?php
/**
 *
 * @author Stefano Torresi (http://stefanotorresi.it)
 * @license See the file LICENSE.txt for copying permission.
 * ************************************************
 */

namespace MyBackend\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\ViewModel;

class AdminController extends AbstractActionController
{
    public function indexAction()
    {
        return false;
    }

    public function loginAction()
    {
        /** @var \MyBackend\Module $module  */
        $module = $this->getServiceLocator()->get('ModuleManager')->getModule('MyBackend');
        $parentRoute = $module->getOption('routes.backend');

        /** @var \ZfcUser\Options\ModuleOptions $zfcUserOptions  */
        $zfcUserOptions = $this->getServiceLocator()->get('zfcuser_module_options');
        $zfcUserOptions->setLoginRedirectRoute($parentRoute);

        $controller = $this;
        $this->getEvent()->getApplication()->getEventManager()->attach(
            MvcEvent::EVENT_RENDER,
            function(MvcEvent $e) use ($controller, $parentRoute) {
                foreach($e->getViewModel()->getIterator() as $child) {
                    /** @var ViewModel $child  */
                    if ($child->captureTo() === 'content') {
                        $child->setTemplate('my-backend/login');
                        $child->redirect = $controller->url()->fromRoute($parentRoute);
                    }
                }
            },
            1000
        );

        return $this->forward()->dispatch('zfcuser');
    }
}
