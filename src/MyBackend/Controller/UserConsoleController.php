<?php
/**
 * @author Stefano Torresi (http://stefanotorresi.it)
 * @license See the file LICENSE.txt for copying permission.
 * ************************************************
 */

namespace MyBackend\Controller;

use MyBase\Controller\AbstractConsoleController;
use MyBackend\Entity;
use Zend\Console\Adapter\Posix;
use Zend\Console\ColorInterface;
use Zend\Console\Console;
use Zend\Console\Prompt;
use Zend\Permissions\Rbac\Rbac;
use ZfcUser\Service\User as UserService;

class UserConsoleController extends AbstractConsoleController
{
    /**
     * @var UserService $userService
     */
    protected $userService;

    /**
     * @var Rbac $rbac
     */
    protected $rbac;

    public function createAdminAction()
    {
        $username       = $this->params('username') ?: Prompt\Line::prompt('Please enter a username: ');
        $email          = $this->params('email') ?: Prompt\Line::prompt('Please enter an email: ');
        $displayName    = $this->getUserService()->getOptions()->getEnableDisplayName() ?
            Prompt\Line::prompt('Please enter a display name: ') : null;

        $console = Console::getInstance();
        if ($console instanceof Posix) {
            shell_exec('stty -echo');
        }
        $password       = Prompt\Line::prompt('Please enter a password: ');
        $passwordVerify = Prompt\Line::prompt('Please confirm the password: ');
        shell_exec('stty echo');

        $console->showCursor();

        $admin = $this->getUserService()->register([
            'username'          => $username,
            'email'             => $email,
            'display_name'      => $displayName,
            'password'          => $password,
            'passwordVerify'    => $passwordVerify,
        ]);

        if (! $admin) {

            $output[] = PHP_EOL.PHP_EOL.$console->colorize('Invalid data provided', ColorInterface::RED).PHP_EOL;

            $form = $this->getUserService()->getRegisterForm();

            foreach ($form->getMessages() as $field => $messages) {
                foreach ($messages as $message) {
                    $output[] = $form->get($field)->getLabel(). ': '.$message;
                }
            }

            return implode(PHP_EOL, $output).PHP_EOL;
        }

        $adminRole = $this->getRbac()->getRole('admin');

        if (! $adminRole) {
            return "'admin' role not found. Did you run 'data-fixture:import' ?";
        }

        $admin->addRole($adminRole);
        $this->getUserService()->getUserMapper()->update($admin);

        return PHP_EOL.PHP_EOL.$console->colorize(sprintf('User \'%s\' added', $username), ColorInterface::GREEN);
    }

    public function deleteAction()
    {
        $search     = [];
        $searchKeys = ['username', 'email'];
        $console    = Console::getInstance();

        foreach ($searchKeys as $key) {
            $param = $this->params($key);
            if ($param) {
                $search[$key] = $param;
            }
        }

        if (empty($search)) {
            $search['unknown'] = Prompt\Line::prompt('Please enter username or email: ');
        }

        $user = null;

        foreach ($searchKeys as $searchKey) {
            $searchMultipleKeys = true;
            $searchMethod       = 'findBy'.$searchKey;
            $searchValue        = null;

            if (array_key_exists($searchKey, $search)) {
                $searchValue = $search[$searchKey];
                $searchMultipleKeys = false;
            } elseif ( isset($search['unknown']) ) {
                $searchValue = $search['unknown'];
            }

            if (! $searchValue) {
                continue;
            }

            $user = $this->getUserService()->getUserMapper()->$searchMethod($searchValue);

            if (! $searchMultipleKeys || $user) {
                break;
            }
        }

        if (! $user instanceof Entity\User) {
            return PHP_EOL.$console->colorize('User not found', ColorInterface::RED).PHP_EOL;
        }

        echo
            "User found".PHP_EOL
            ." Id: \t\t"            .$user->getId().PHP_EOL
            ." Username: \t"        .$user->getUsername().PHP_EOL
            ." Email: \t"           .$user->getEmail().PHP_EOL
            ." Display name: \t"    .$user->getDisplayName().PHP_EOL
            ." State: \t"           .$user->getState().PHP_EOL
            ." Roles: \t"           .implode(', ',$user->getRoles()).PHP_EOL.PHP_EOL
        ;

        $confirm = Prompt\Confirm::prompt($console->colorize(
            'Are you sure you want to delete this user? [y/n]',
            ColorInterface::YELLOW
        ));

        if (! $confirm) {
            return PHP_EOL.$console->colorize('Aborted', ColorInterface::LIGHT_RED).PHP_EOL;
        }

        $this->getUserService()->getUserMapper()->delete($user);

        return PHP_EOL.$console->colorize(
            sprintf("User '%s' deleted", $user->getUsername()),
            ColorInterface::GREEN
        ).PHP_EOL;
    }

    /**
     * @return UserService
     */
    public function getUserService()
    {
        if (! $this->userService) {
            $this->userService = $this->getServiceLocator()->get('zfcuser_user_service');
        }

        return $this->userService;
    }

    /**
     * @param UserService $userService
     */
    public function setUserService($userService)
    {
        $this->userService = $userService;
    }

    /**
     * @return \Zend\Permissions\Rbac\Rbac
     */
    public function getRbac()
    {
        if (! $this->rbac) {
            $this->rbac = $this->getServiceLocator()->get('ZfcRbac\Service\AuthorizationService')->getRbac();
        }

        return $this->rbac;
    }

    /**
     * @param \Zend\Permissions\Rbac\Rbac $rbac
     */
    public function setRbac($rbac)
    {
        $this->rbac = $rbac;
    }
}
