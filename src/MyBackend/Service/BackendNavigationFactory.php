<?php
/**
 * @author Stefano Torresi (http://stefanotorresi.it)
 * @license See the file LICENSE.txt for copying permission.
 * ************************************************
 */

namespace MyBackend\Service;

use MyBackend\BackendModuleInterface;
use Zend\Navigation\Service\AbstractNavigationFactory;
use Zend\ServiceManager\ServiceLocatorInterface;

class BackendNavigationFactory extends AbstractNavigationFactory
{
    public function getName()
    {
        return 'backend';
    }

    protected function getPages(ServiceLocatorInterface $serviceLocator)
    {
        $loadedModules = $serviceLocator->get('ModuleManager')->getLoadedModules();

        foreach ($loadedModules as $module) {
            if ($module instanceof BackendModuleInterface) {
                $pages[] = $module->getNavPage();
            }
        }

        $this->pages = $this->preparePages($serviceLocator, $pages);

        return $this->pages;
    }
}
