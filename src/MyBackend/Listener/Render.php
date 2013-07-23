<?php
/**
 * @author Stefano Torresi (http://stefanotorresi.it)
 * @license See the file LICENSE.txt for copying permission.
 * ************************************************
 */

namespace MyBackend\Listener;

use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventManagerInterface;
use Zend\Mvc\MvcEvent;
use Zend\View\Model;

class Render extends AbstractListenerAggregate
{
    /**
     * {@inheritDoc}
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_ROUTE, array($this, 'selectModule'), -1);
        $this->listeners[] = $events->attach(MvcEvent::EVENT_RENDER, array($this, 'prepareLayout'), -1);
        $this->listeners[] = $events->attach(MvcEvent::EVENT_RENDER_ERROR, array($this, 'prepareLayout'), -101);
    }

    public function selectModule(MvcEvent $e)
    {
        $routeName = $e->getRouteMatch()->getMatchedRouteName();
        /** @var \MyBackend\Module $module  */
        $module = $e->getApplication()->getServiceManager()->get('ModuleManager')->getModule('MyBackend');
        $parentRoute = $module->getOption('routes.backend');

        if (strpos($routeName, $parentRoute) !== 0) {
            return;
        }

        $rootModel = $e->getViewModel();

        $rootModel->namespace = $module->getNamespace();
    }

    public function prepareLayout(MvcEvent $e)
    {
        $serviceManager = $e->getApplication()->getServiceManager();

        /** @var \MyBackend\Module $module  */
        $module = $serviceManager->get('ModuleManager')->getModule('MyBackend');

        $rootModel = $e->getViewModel();

        if (! $rootModel instanceof Model\ViewModel || $rootModel->namespace !== $module->getNamespace() ) {
            return;
        }

        $rootModel->setVariables($module->getOptions('view_params'));

        if ($e->isError()) {
            $rootModel->error = true;
        }

        if ($rootModel->terminate()) {
            return;
        }

        $viewHelperManager = $serviceManager->get('ViewHelperManager');
        $viewHelperManager->get('translate')->setTranslatorTextDomain($rootModel->namespace);
        $viewHelperManager->get('navigation')->setTranslatorTextDomain($rootModel->namespace);
        $viewHelperManager->get('formlabel')->setTranslatorTextDomain($rootModel->namespace);
        $viewHelperManager->get('formrow')->setTranslatorTextDomain($rootModel->namespace);
        $viewHelperManager->get('flashmessenger')->setTranslatorTextDomain($rootModel->namespace);

        $rootModel->setTemplate('my-backend/layout/layout');

        $header = new Model\ViewModel();
        $header->setTemplate('my-backend/layout/header');
        $header->setVariable('routes', $module->getOption('routes'));

        $footer = new Model\ViewModel();
        $footer->setTemplate('my-backend/layout/footer');

        $javascript = new Model\ViewModel();
        $javascript->setTemplate('my-backend/layout/javascript');

        $rootModel->addChild($header, 'header')
            ->addChild($footer, 'footer')
            ->addChild($javascript, 'javascript');
    }

}
