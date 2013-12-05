<?php
/**
 * @author Stefano Torresi (http://stefanotorresi.it)
 * @license See the file LICENSE.txt for copying permission.
 * ************************************************
 */

namespace MyBackend\Navigation;

use MyBackend\BackendModuleInterface;
use Zend\Navigation\Service\AbstractNavigationFactory;
use Zend\ServiceManager\ServiceLocatorInterface;

class BackendNavigationFactory extends AbstractNavigationFactory
{
    public function getName()
    {
        return 'backend';
    }
}
