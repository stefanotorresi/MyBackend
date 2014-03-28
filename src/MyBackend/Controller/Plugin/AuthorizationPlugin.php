<?php
/**
 * @author Stefano Torresi (http://stefanotorresi.it)
 * @license See the file LICENSE.txt for copying permission.
 * ************************************************
 */

namespace MyBackend\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use ZfcRbac\Permission\PermissionInterface;
use ZfcRbac\Service\AuthorizationService;
use ZfcRbac\Service\AuthorizationServiceAwareInterface;
use ZfcRbac\Service\AuthorizationServiceAwareTrait;

class AuthorizationPlugin extends AbstractPlugin implements AuthorizationServiceAwareInterface
{
    use AuthorizationServiceAwareTrait;

    /**
     * @param AuthorizationService $authorizationService
     */
    public function __construct(AuthorizationService $authorizationService)
    {
        $this->setAuthorizationService($authorizationService);
    }

    /**
     * @param  string|PermissionInterface $permission
     * @param  mixed                      $context
     * @return self|bool
     */
    public function __invoke($permission = null, $context = null)
    {
        if (! $permission) {
            return $this;
        }

        return $this->isGranted($permission, $context);
    }

    /**
     * @param  string|PermissionInterface $permission
     * @param  mixed                      $context
     * @return bool
     */
    public function isGranted($permission, $context = null)
    {
        return $this->authorizationService->isGranted($permission, $context);
    }
}
