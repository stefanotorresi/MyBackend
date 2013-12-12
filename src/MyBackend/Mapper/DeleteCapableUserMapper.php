<?php
/**
 * @author Stefano Torresi (http://stefanotorresi.it)
 * @license See the file LICENSE.txt for copying permission.
 * ************************************************
 */

namespace MyBackend\Mapper;

interface DeleteCapableUserMapper
{
    public function delete($whereOrEntity, $tableName = null);
}
