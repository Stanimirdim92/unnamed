<?php

/**
 * @copyright  2015 (c) Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 *
 * @version    0.0.21
 *
 * @link       TBA
 */

namespace Application\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Application\Exception\AuthorizationException;
use Zend\Mvc\Controller\Plugin\Redirect;
use Zend\Authentication\AuthenticationService;

final class UserData extends AbstractPlugin
{
    /**
     * @var AuthenticationService
     */
    private $auth;

    /**
     * @var Redirect
     */
    private $redirect;

    /**
     * @param Redirect $redirect
     */
    public function __construct(Redirect $redirect)
    {
        $this->redirect = $redirect;
        $this->auth = new AuthenticationService();
    }

    /**
     * Clear all session data and identities.
     * Throw an exception, which will be captured by the event manager and logged.
     *
     * @param string $errorString
     *
     * @throws AuthorizationException
     */
    public function clearUserData($errorString)
    {
        $this->auth->clearIdentity();
        throw new AuthorizationException($errorString);
    }

    /**
     * See if user is logged in.
     *
     * First we check to see if there is an identity stored.
     * If there is, we need to check for two parameters role and logged.
     * Those 2 parameters MUST always be of types int and bool.
     *
     * The redirect serves parameter is used to determinated
     * if we need to redirect the user to somewhere else or just leave him access the requested area
     *
     * @param bool $redirect
     * @param string $errorString
     * @param string $url
     *
     * @return mixed
     */
    public function checkIdentity($redirect = true, $errorString = "Error", $url = "/")
    {
        if ($this->auth->hasIdentity()) {
            if (isset($this->auth->getIdentity()["id"])) {
                /*
                 * If everything went fine, just return true and let the user access the requested area or make a redirect
                 */
                if ($redirect === false) {
                    return true;
                }
                return $this->redirect->toUrl($url);
            }
            return $this->clearUserData($errorString); // something is wrong, clear all user data
        }
        return null;
    }

    /**
     * @return array
     */
    public function getIdentity()
    {
        return $this->auth->getIdentity();
    }
}
