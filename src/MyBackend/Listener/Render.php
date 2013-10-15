<?php
/**
 * @author Stefano Torresi (http://stefanotorresi.it)
 * @license See the file LICENSE.txt for copying permission.
 * ************************************************
 */

namespace MyBackend\Listener;

use MyBackend\Module as MyBackend;
use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventManagerInterface;
use Zend\ModuleManager\ModuleManager;
use Zend\Mvc\MvcEvent;
use Zend\View\Model;

class Render extends AbstractListenerAggregate
{
    /**
     * {@inheritDoc}
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_RENDER, array($this, 'prepareLayout'), -1);
        $this->listeners[] = $events->attach(MvcEvent::EVENT_RENDER_ERROR, array($this, 'prepareLayout'), -101);
    }

    public function prepareLayout(MvcEvent $e)
    {
        $serviceManager = $e->getApplication()->getServiceManager();

        $module = $e->getParam('module');
        if (! $module instanceof MyBackend) {
            return;
        }

        $rootModel = $e->getViewModel();
        if (! $rootModel instanceof Model\ViewModel) {
            return;
        }

        $rootModel->setVariables($module->getOptions('view_params'));

        if ($e->isError()) {
            $rootModel->error = true;
        }

        if ($rootModel->terminate()) {
            return;
        }

        $translatorDomain = 'MyBackend';

        $viewHelperManager = $serviceManager->get('ViewHelperManager');
        $viewHelperManager->get('translate')->setTranslatorTextDomain($translatorDomain);
        $viewHelperManager->get('navigation')->setTranslatorTextDomain($translatorDomain);
        $viewHelperManager->get('formlabel')->setTranslatorTextDomain($translatorDomain);
        $viewHelperManager->get('formrow')->setTranslatorTextDomain($translatorDomain);
        $viewHelperManager->get('flashmessenger')->setTranslatorTextDomain($translatorDomain);
        $viewHelperManager->get('ztbnavigation')->setTranslator($serviceManager->get('translator'))
            ->setTranslatorTextDomain($translatorDomain);

        $rootModel->displayLangNav = $serviceManager->has('nav-lang');
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
