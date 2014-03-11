<?php
/**
 * @author Stefano Torresi (http://stefanotorresi.it)
 * @license See the file LICENSE.txt for copying permission.
 * ************************************************
 */

namespace MyBackend\Navigation;

use MyBackend\Options\ModuleOptions;
use Zend\Mvc\Application;
use Zend\Mvc\Router\RouteMatch;
use Zend\Navigation\Navigation;
use Zend\Navigation\Page\Mvc as MvcPage;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class BackendBreadcrumbsFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param  ServiceLocatorInterface $serviceLocator
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
            'router'        => $router,
            'routeMatch'    => $routeMatch ?: new RouteMatch([])
        ]);

        foreach ($navBackend->getPages() as $page) {
            $home->addPage($page);
        }

        $pages = [$home];

        return new Navigation($pages);
    }
}
