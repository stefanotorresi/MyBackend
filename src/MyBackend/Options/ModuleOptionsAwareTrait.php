<?php
/**
 * @author Stefano Torresi (http://stefanotorresi.it)
 * @license See the file LICENSE.txt for copying permission.
 * ************************************************
 */

namespace MyBackend\Options;

trait ModuleOptionsAwareTrait
{
    /**
     * @var ModuleOptions
     */
    protected $moduleOptions;

    /**
     * @param  ModuleOptions $moduleOptions
     * @return self
     */
    public function setModuleOptions(ModuleOptions $moduleOptions)
    {
        $this->moduleOptions = $moduleOptions;

        return $this;
    }

    /**
     * @return ModuleOptions
     */
    public function getModuleOptions()
    {
        return $this->moduleOptions ?: $this->moduleOptions = new ModuleOptions();
    }

}
