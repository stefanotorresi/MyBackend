<?php
/**
 * @author Stefano Torresi (http://stefanotorresi.it)
 * @license See the file LICENSE.txt for copying permission.
 * ************************************************
 */

namespace MyBackend\Controller;

use Exception;
use MyBase\Controller\AbstractConsoleController;
use MyBackend\Entity;
use MyBackend\Service\UserService;
use Zend\Console\Adapter\Posix;
use Zend\Console\ColorInterface as Color;
use Zend\Console\Console;
use Zend\Console\Prompt;

class UserConsoleController extends AbstractConsoleController
{
    /**
     * @var UserService $userService
     */
    protected $userService;

    /**
     *
     */
    public function createAction()
    {
        $console = $this->getConsole();

        $data['email']      = $this->params('email') ?: Prompt\Line::prompt('Please enter an email: ', false, 255);

        $data['username']   = $this->getUserService()->getOptions()->getEnableUsername() ?
            ($this->params('username') ?: Prompt\Line::prompt('Please enter a username: ', false, 255))
            : null;

        $data['displayName'] = $this->getUserService()->getOptions()->getEnableDisplayName() ?
            Prompt\Line::prompt('Please enter a display name: ', false, 50)
            : null;


        if ($console instanceof Posix) {
            shell_exec('stty -echo');
        }

        $data['password']       = Prompt\Line::prompt('Please enter a password: ');
        $console->writeLine();
        $data['passwordVerify'] = Prompt\Line::prompt('Please confirm the password: ');

        if ($console instanceof Posix) {
            shell_exec('stty echo');
        }

        /**
         * @todo instead of removing unknown inputs, collect data dynamically
         */
        $form = $this->getUserService()->getRegisterForm();
        $collectedElements = array_keys($data);
        foreach ($form as $element) {
            $elementName = $element->getName();
            if (! in_array($elementName, $collectedElements)) {
                $form->remove($elementName);
                $form->getInputFilter()->remove($elementName);
            }
        }

        /** @var Entity\AbstractUser $user */
        $user = $this->getUserService()->register($data);

        if (! $user) {

            $console->writeLine(PHP_EOL.'Invalid data provided', Color::RED);

            $form = $this->getUserService()->getRegisterForm();

            foreach ($form->getMessages() as $field => $messages) {
                foreach ($messages as $message) {
                    $console->writeLine(($form->get($field)->getLabel() ?: $field) . ': ' . $message);
                }
            }

            return;
        }

        $console->writeLine();

        $roles = $this->params('roles') ?: Prompt\Line::prompt(
            'Please enter a comma separated list of user roles: [guest] ',
            true,
            32
        );

        if (empty($roles)) {
            $roles = 'guest';
        }

        $roles = explode(',', $roles);

        try {
            $this->getUserService()->addRolesToUser($roles, $user);
        } catch (Exception $e) {
            $userMapper = $this->getUserService()->getUserMapper();
            $userMapper->remove($user); // rollback if we can't update user with roles
            $console->writeLine();
            $console->writeLine("Error: ".$e->getMessage(), Color::RED);
        }

        $console->writeLine();
        $console->writeLine(sprintf('User \'%s\' added', $user->getUsername() ?: $user->getEmail()), Color::GREEN);
    }

    public function deleteAction()
    {
        $console    = Console::getInstance();
        $search     = [];
        $searchKeys = ['email', 'username'];

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
            } elseif (isset($search['unknown'])) {
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

        if (! $user instanceof Entity\AbstractUser) {
            $console->writeLine(PHP_EOL.'User not found', Color::RED);

            return;
        }

        $console->writeLine(
            "User found" . PHP_EOL
            ." Id: \t\t" . $user->getId() . PHP_EOL
            .($user->getUsername() ? " Username: \t" . $user->getUsername() . PHP_EOL : '')
            ." Email: \t" . $user->getEmail() . PHP_EOL
            .($user->getDisplayName() ? " Display name: \t" . $user->getDisplayName() . PHP_EOL : '')
            .($user->getState() !== null ? " State: \t" . $user->getState() . PHP_EOL : '')
            .($user->getRoles()->count() ? " Roles: \t" . implode(', ', $user->getRoles()->toArray()) : '')
        );

        $confirm = Prompt\Confirm::prompt($console->colorize(
            PHP_EOL.'Are you sure you want to delete this user? [y/n]',
            Color::YELLOW
        ));

        if (! $confirm) {
            $console->writeLine(PHP_EOL.'Aborted', Color::LIGHT_RED);

            return;
        }

        $this->getUserService()->getUserMapper()->remove($user);

        $console->writeLine(
            PHP_EOL.sprintf("User '%s' deleted", $user->getUsername() ?: $user->getEmail()),
            Color::GREEN
        );
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
}
