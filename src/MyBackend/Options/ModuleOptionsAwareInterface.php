<?php
/**
 * @author Stefano Torresi (http://stefanotorresi.it)
 * @license See the file LICENSE.txt for copying permission.
 * ************************************************
 */

namespace MyBackend\Options;

interface ModuleOptionsAwareInterface
{
    /**
     * @param  ModuleOptions $moduleOptions
     * @return self
     */
    public function setModuleOptions(ModuleOptions $moduleOptions);

    /**
     * @return ModuleOptions
     */
    public function getModuleOptions();
}
