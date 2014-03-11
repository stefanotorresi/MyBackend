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
use Zend\Mvc\MvcEvent;
use Zend\View\Model;
use ZfcRbac\Exception\UnauthorizedException;

class Render extends AbstractListenerAggregate
{
    /**
     * {@inheritDoc}
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_RENDER, [$this, 'prepareLayout'], -1);
        $this->listeners[] = $events->attach(MvcEvent::EVENT_RENDER_ERROR, [$this, 'prepareLayout'], -101);
    }

    public function prepareLayout(MvcEvent $e)
    {
        $module = $e->getParam('module');
        if (! $module instanceof MyBackend) {
            return;
        }

        if ($e->isError() && $e->getParam('exception') instanceof UnauthorizedException) {
            $e->setParam('module', null);
            return;
        }

        $layoutModel = $e->getViewModel();
        if (! $layoutModel instanceof Model\ViewModel || $layoutModel instanceof Model\JsonModel) {
            return;
        }

        $serviceManager = $e->getApplication()->getServiceManager();

        /** @var ModuleOptions $options  */
        $options    = $serviceManager->get('MyBackend\Options\ModuleOptions');

        $layoutModel->setVariables([
            'title'             => $options->getTitle(),
            'cacheBustIndex'    => $options->getCacheBustIndex(),
            'backendRoute'      => $options->getBackendRoute(),
            'frontendRoute'     => $options->getFrontendRoute(),
            'backendLoginRoute' => $options->getBackendLoginRoute(),
            'postLogoutRoute'   => $options->getPostLogoutRoute(),
            'error'             => $e->isError(),
            'i18nEnabled'       => (bool) $serviceManager->get('ModuleManager')->getModule('MyI18n'),
        ]);
        $layoutModel->setTemplate($options->getTemplate());

        $translatorDomain = $options->getTranslatorTextDomain();

        $viewHelperManager = $serviceManager->get('ViewHelperManager');
        $viewHelperManager->get('translate')->setTranslatorTextDomain($translatorDomain);
        $viewHelperManager->get('navigation')->setTranslatorTextDomain($translatorDomain);
        $viewHelperManager->get('formlabel')->setTranslatorTextDomain($translatorDomain);
        $viewHelperManager->get('formrow')->setTranslatorTextDomain($translatorDomain);
        $viewHelperManager->get('flashmessenger')->setTranslatorTextDomain($translatorDomain);
        $viewHelperManager->get('ztbnavigation')->setTranslatorTextDomain($translatorDomain);
        $viewHelperManager->get('headtitle')->setTranslatorTextDomain($translatorDomain);
        $viewHelperManager->get('formElementErrors')->setTranslatorTextDomain($translatorDomain);
    }

}
