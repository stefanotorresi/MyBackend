<?php
/**
 * @author Stefano Torresi (http://stefanotorresi.it)
 * @license See the file LICENSE.txt for copying permission.
 * ************************************************
 */

namespace MyBackend\Service;

interface UserServiceAwareInterface
{
    /**
     * @return UserServiceInterface
     */
    public function getUserService();

    /**
     * @param UserServiceInterface $userService
     */
    public function setUserService(UserServiceInterface $userService);
}
