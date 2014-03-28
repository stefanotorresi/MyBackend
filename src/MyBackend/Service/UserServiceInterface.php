<?php
/**
 * @author Stefano Torresi (http://stefanotorresi.it)
 * @license See the file LICENSE.txt for copying permission.
 * ************************************************
 */

namespace MyBackend\Service;

use MyBackend\Mapper\UserMapperInterface;
use Zend\Authentication\AuthenticationServiceInterface;
use ZfcUser\Form\Login as LoginForm;
use ZfcUser\Form\Register as RegisterForm;
use ZfcUser\Options\ModuleOptions;

interface UserServiceInterface
{
    /**
     * @return UserMapperInterface
     */
    public function getUserMapper();

    /**
     * @return AuthenticationServiceInterface
     */
    public function getAuthService();

    /**
     * @return RegisterForm
     */
    public function getRegisterForm();

    /**
     * @return LoginForm
     */
    public function getLoginForm();

    /**
     * @return ModuleOptions
     */
    public function getOptions();
}
