<?php
/**
 * @author Stefano Torresi (http://stefanotorresi.it)
 * @license See the file LICENSE.txt for copying permission.
 * ************************************************
 *
 * This file is placed here for compatibility with Zendframework 2's ModuleManager.
 * It allows usage of this module even without composer.
 * The original Module.php is in 'src/{ModuleNamespace}' in order to respect PSR-0
 */

namespace MyBackend;

require_once __DIR__ . '/src/' . __NAMESPACE__ . '/Module.php';
