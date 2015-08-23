<?php
/**
 * MIT License
 * ===========
 *
 * Copyright (c) 2015 Stanimir Dimitrov <stanimirdim92@gmail.com>
 *
 * Permission is hereby granted, free of charge, to any person obtaining
 * a copy of this software and associated documentation files (the
 * "Software"), to deal in the Software without restriction, including
 * without limitation the rights to use, copy, modify, merge, publish,
 * distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to
 * the following conditions:
 *
 * The above copyright notice and this permission notice shall be included
 * in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS
 * OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
 * IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY
 * CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT,
 * TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE
 * SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 *
 * @author     Stanimir Dimitrov <stanimirdim92@gmail.com>
 * @copyright  2015 Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 * @version    0.0.10
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
     * if we need to redirect the user to somewhere
     * else or just leave him access the desired area
     *
     * @param bool $redirect
     * @param string $errorString
     * @param string $url
     * @throws AuthorizationException
     * @return mixed
     */
    public function checkIdentity($redirect = true, $errorString = null, $url = "/")
    {
        if ($this->auth->hasIdentity()) {
            if (!empty($this->auth->getIdentity()->role) &&
              ((int) $this->auth->getIdentity()->role === 1 || (int) $this->auth->getIdentity()->role === 10) &&
              isset($this->auth->getIdentity()->logged) && $this->auth->getIdentity()->logged === true) {
                /**
                 * If everything went fine, just return true and let the user access the desired area or make a redirect
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
     * @return \stdClass
     */
    public function getIdentity()
    {
        return $this->auth->getIdentity();
    }
}
