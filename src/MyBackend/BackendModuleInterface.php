<?php
/**
 * @author Stefano Torresi (http://stefanotorresi.it)
 * @license See the file LICENSE.txt for copying permission.
 * ************************************************
 */

namespace MyBackend;

interface BackendModuleInterface
{
    /**
     * The Navigation page used by backend navigation factory
     *
     * @return mixed
     */
    public function getNavPage();
}
