<?php

/**
 * @copyright  2015 (c) Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 *
 * @version    0.0.15
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
     * @var AuthenticationService $auth
     */
    private $auth = null;

    /**
     * @var Redirect $redirect
     */
    private $redirect = null;

    /**
     * @param Redirect $redirect
     */
    public function __construct(Redirect $redirect = null)
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
    public function clearUserData($errorString = null)
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
    public function checkIdentity($redirect = true, $errorString = null, $url = "/")
    {
        if ($this->auth->hasIdentity()) {
            if (!empty($this->auth->getIdentity()->role) &&
              ((int) $this->auth->getIdentity()->role === 1 || (int) $this->auth->getIdentity()->role === 10) &&
              isset($this->auth->getIdentity()->logged) && $this->auth->getIdentity()->logged === true) {
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
        return;
    }

    /**
     * @return \stdClass
     */
    public function getIdentity()
    {
        return $this->auth->getIdentity();
    }
}
