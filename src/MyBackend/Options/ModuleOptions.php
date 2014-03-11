<?php
/**
 * @author Stefano Torresi (http://stefanotorresi.it)
 * @license See the file LICENSE.txt for copying permission.
 * ************************************************
 */

namespace MyBackend\Options;

use Zend\Stdlib\AbstractOptions;

class ModuleOptions extends AbstractOptions
{
    /**
     * @var bool $disableFrontendLogin
     */
    protected $disableFrontendLogin = true;

    /**
     * @var string $backendRoute
     */
    protected $backendRoute = 'admin';

    /**
     * @var string $frontendRoute
     */
    protected $frontendRoute = 'home';

    /**
     * @var string $loginRoute
     */
    protected $backendLoginRoute = 'admin/login';

    /**
     * @var string $postLogoutRoute
     */
    protected $postLogoutRoute = 'admin';

    /**
     * @var string $translatorDomain
     */
    protected $translatorTextDomain = 'MyBackend';

    /**
     * @var string $template
     */
    protected $template = 'my-backend/layout/layout';

    /**
     * @var int $cacheBustIndex
     */
    protected $cacheBustIndex;

    /**
     * @var string $title
     */
    protected $title = 'My Backend';

    /**
     * @return boolean
     */
    public function getDisableFrontendLogin()
    {
        return $this->disableFrontendLogin;
    }

    /**
     * @param boolean $disableFrontendLogin
     */
    public function setDisableFrontendLogin($disableFrontendLogin)
    {
        $this->disableFrontendLogin = $disableFrontendLogin;
    }

    /**
     * @return string
     */
    public function getBackendRoute()
    {
        return $this->backendRoute;
    }

    /**
     * @param string $backendRoute
     */
    public function setBackendRoute($backendRoute)
    {
        $this->backendRoute = $backendRoute;
    }

    /**
     * @return string
     */
    public function getFrontendRoute()
    {
        return $this->frontendRoute;
    }

    /**
     * @param string $frontendRoute
     */
    public function setFrontendRoute($frontendRoute)
    {
        $this->frontendRoute = $frontendRoute;
    }

    /**
     * @return string
     */
    public function getBackendLoginRoute()
    {
        return $this->backendLoginRoute;
    }

    /**
     * @param string $loginRoute
     */
    public function setBackendLoginRoute($loginRoute)
    {
        $this->backendLoginRoute = $loginRoute;
    }

    /**
     * @return string
     */
    public function getPostLogoutRoute()
    {
        return $this->postLogoutRoute;
    }

    /**
     * @param string $postLogoutRoute
     */
    public function setPostLogoutRoute($postLogoutRoute)
    {
        $this->postLogoutRoute = $postLogoutRoute;
    }

    /**
     * @return string
     */
    public function getTranslatorTextDomain()
    {
        return $this->translatorTextDomain;
    }

    /**
     * @param string $translatorDomain
     */
    public function setTranslatorTextDomain($translatorDomain)
    {
        $this->translatorTextDomain = $translatorDomain;
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * @param string $template
     */
    public function setTemplate($template)
    {
        $this->template = $template;
    }

    /**
     * @return int
     */
    public function getCacheBustIndex()
    {
        if (! $this->cacheBustIndex) {
            $this->cacheBustIndex = mt_rand();
        }

        return $this->cacheBustIndex;
    }

    /**
     * @param int $cacheBustIndex
     */
    public function setCacheBustIndex($cacheBustIndex)
    {
        $this->cacheBustIndex = $cacheBustIndex;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }
}
