<?php
/**
 * @author Stefano Torresi (http://stefanotorresi.it)
 * @license See the file LICENSE.txt for copying permission.
 * ************************************************
 */

namespace MyBackend\Navigation;

use Zend\Navigation\Service\AbstractNavigationFactory;

class BackendNavigationFactory extends AbstractNavigationFactory
{
    public function getName()
    {
        return 'backend';
    }
}
