<?php
/**
 * @author Stefano Torresi (http://stefanotorresi.it)
 * @license See the file LICENSE.txt for copying permission.
 * ************************************************
 */

namespace MyBackend\Navigation;

use MyBackend\Options\ModuleOptions;
use Zend\Mvc\Application;
use Zend\Navigation\Navigation;
use Zend\Navigation\Page\Mvc as MvcPage;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class BackendBreadcrumbsFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var ModuleOptions $options */
        $options = $serviceLocator->get('MyBackend\Options\ModuleOptions');

        /** @var Navigation $navBackend */
        $navBackend = $serviceLocator->get('MyBackend\Navigation\BackendNavigation');

        /** @var Application $application */
        $application = $serviceLocator->get('Application');
        $routeMatch  = $application->getMvcEvent()->getRouteMatch();
        $router      = $application->getMvcEvent()->getRouter();

        $home = new MvcPage([
            'label'         => 'Backend',
            'route'         => $options->getBackendRoute(),
            'pages'         => $navBackend->getPages(),
            'router'        => $router,
            'routeMatch'    => $routeMatch
        ]);

        $pages = [$home];

        return new Navigation($pages);
    }
}
