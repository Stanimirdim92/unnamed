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
 * @category   Application\Login
 * @package    Unnamed
 * @author     Stanimir Dimitrov <stanimirdim92@gmail.com>
 * @copyright  2015 Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 * @version    0.0.3
 * @link       TBA
 */

namespace Application\Controller;

use Zend\Authentication\AuthenticationService;
use Zend\Session\Container;
use Zend\Http\PhpEnvironment\RemoteAddress;
use Zend\Mvc\MvcEvent;
use Zend\Authentication\Adapter\DbTable\CallbackCheckAdapter;
use Custom\Plugins\Mailing;
use Custom\Plugins\Functions;
use Application\Model\ResetPassword;
use Application\Form\LoginForm;
use Application\Form\ResetPasswordForm;
use Application\Form\NewPasswordForm;
require '/vendor/Custom/Plugins/Password.php';

class LoginController extends IndexController
{
    /**
     * User access
     */
    const ROLE_USER = 1;

    /**
     * Admin access
     */
    const ROLE_ADMIN = 10;

    /**
     * @var Zend\Db\Adapter\Adapter
     */
    private $adapter = null;

    /**
     * @var Application\Form\ResetPasswordForm
     */
    private $resetPasswordForm = null;

    /**
     * @var Application\Form\NewPasswordForm
     */
    private $newPasswordForm = null;

    /**
     * @var Application\Form\LoginForm
     */
    private $loginForm = null;

    /**
     * @param Application\Form\LoginForm $contactForm
     * @param Zend\Db\Adapter\Adapter $adapter
     * @param Application\Form\ResetPasswordForm $resetPasswordForm
     */
    public function __construct(LoginForm $loginForm = null, $adapter = null, ResetPasswordForm $resetPasswordForm = null, NewPasswordForm $newPasswordForm = null)
    {
        parent::__construct();

        /**
         * Handle ServiceManager dependencies
         */
        $this->loginForm = $loginForm;
        $this->adapter = $adapter;
        $this->resetPasswordForm = $resetPasswordForm;
        $this->newPasswordForm = $newPasswordForm;
    }

    /**
     * @param  MvcEvent $e
     */
    public function onDispatch(MvcEvent $e)
    {
        parent::onDispatch($e);

        /**
         * If user is logged and tries to access one of the given actions
         * he will be redirected to the root url of the website.
         * For resetpassword and newpassword actions we assume that the user is not logged in.
         */
        $this->checkIdentity();
    }

    /**
     * Get database and check if given email and password matches.
     *
     * @param array $options
     * @var Zend\Crypt\Password\Bcrypt
     * @var Zend\Authentication\Adapter\DbTable\CallbackCheckAdapter
     * @return DbTable|Adapter
     */
    private function getAuthAdapter(array $options = [])
    {
        $credentialCallback = function ($passwordInDatabase, $passwordProvided) {
            return password_verify($passwordProvided, $passwordInDatabase);
        };

        $authAdapter = new CallbackCheckAdapter($this->adapter, "user", "email", "password", $credentialCallback);
        $authAdapter->setIdentity((string) $options["email"]);
        $authAdapter->setCredential((string) $options["password"]);

        return $authAdapter;
    }

    /**
     * @var Zend\Form\Form
     * @return LoginForm
     */
    public function indexAction()
    {
        /**
         * @var  Application\Form\LoginForm
         */
        $form = $this->loginForm;
        $form->get("login")->setValue($this->translate("LOGIN"));
        $form->get("email")->setLabel($this->translate("EMAIL"));
        $form->get("password")->setLabel($this->translate("PASSWORD"));
        $this->view->form = $form;
        return $this->view;
    }

    public function processloginAction()
    {
        // Check if we have a POST request
        if (!$this->getRequest()->isPost()) {
            return $this->logoutAction("/login");
        }

        /**
         * @var  Application\Form\LoginForm
         */
        $form = $this->loginForm;
        $form->setInputFilter($form->getInputFilter());
        $form->setData($this->getRequest()->getPost());
        if (!$form->isValid()) {
            $this->setLayoutMessages($form->getMessages(), 'error');
            return $this->logoutAction("/login");
        }

        $adapter = $this->getAuthAdapter($form->getData());
        $auth = new AuthenticationService();
        $result = $auth->authenticate($adapter);
        if (!$result->isValid()) {
            $this->setLayoutMessages($this->translate("LOGIN_ERROR"), 'error');
            return $this->redirect()->toUrl("/login");
        } else {
            $role = self::ROLE_USER;
            $includeRows = ['id', 'name', 'username', 'email', 'deleted', 'image', 'hideEmail', 'userClass', 'ban', 'admin', 'language', 'country'];
            $excludeRows = ['ip', 'password', 'registered', 'lastLogin', 'birthDate', 'salt'];
            $data = $adapter->getResultRowObject($includeRows, $excludeRows);
            $user = $this->getTable('user')->getUser($data->id);

            if ((bool) $user->getDeleted() === 1) {
                $this->setLayoutMessages($this->translate("LOGIN_ERROR"), 'error');
                return $this->logoutAction("/login");
            }
            if ((bool) $user->getAdmin() === 1) {
                $role = self::ROLE_ADMIN;
            }

            $user->setServiceLocator(null);
            $user->setLastLogin(date("Y-m-d H:i:s", time()));
            $remote = new RemoteAddress();
            $user->setIp($remote->getIpAddress());
            $this->getTable('user')->saveUser($user);

            $data->role = (int) $role;
            $data->logged = true;
            $auth->getStorage()->write($data);
            $authSession = new Container('ul'); //user login
            $authSession->setExpirationSeconds(7200); // 2hrs
            return $this->redirect()->toUrl("/");
        }
    }

    /**
     * The resetpasswordAction has generated a random token string.
     * In order to reset the account password, we need to take that token and validate it first.
     * If everything is fine, we let the user to reset his password
     */
    public function newpasswordAction()
    {
        $token = (string) $this->getParam('token', null);
        if (Functions::strLength($token) !== 64) {
            throw new \Exception("Wrong token");
        }

        /**
         * See if token exist or has expired
         */
        $tokenExist = $this->getTable("resetpassword")->fetchList(["user", "token", "date"], "token = '{$token}' AND date >= DATE_SUB(NOW(), INTERVAL 24 HOUR)")->current();
        if (!$tokenExist) {
            $this->setLayoutMessages($this->translate("LINK_EXPIRED"), 'error');
            return $this->redirect()->toUrl("/login");
        }

        $form = $this->newPasswordForm;
        $form->get("password")->setLabel($this->translate("PASSWORD"))->setAttribute("placeholder", $this->translate("PASSWORD"));
        $form->get("repeatpw")->setLabel($this->translate("REPEAT_PASSWORD"))->setAttribute("placeholder", $this->translate("REPEAT_PASSWORD"));
        $form->get("resetpw")->setValue($this->translate("RESET_PW"));

        /**
         * temporary create new view variable to hold the user id.
         * After the password is reset the variable is destroyed.
         * Hidden fields will work, but they are more easier to hack.
         */
        $this->cache->resetpwUserId = $tokenExist["user"];
        $this->view->form = $form;
        return $this->view;
    }

    public function newpasswordprocessAction()
    {
        /**
         * @var  Application\Form\NewPasswordForm
         */
        $form = $this->newPasswordForm;
        if ($this->getRequest()->isPost()) {
            $form->setInputFilter($form->getInputFilter());
            $form->setData($this->getRequest()->getPost());
            if ($form->isValid()) {
                $formData = $form->getData();
                $user = $this->getTable("user")->getUser($this->cache->resetpwUserId);
                $pw = Functions::createPassword($formData["password"]);
                $remote = new RemoteAddress();
                if (!empty($pw)) {
                    $user->setSalt("");
                    $user->setPassword($pw);
                    $user->setIp($remote->getIpAddress());
                    $this->getTable("user")->saveUser($user);
                    $this->setLayoutMessages($this->translate("NEW_PW_SUCCESS"), 'success');
                    unset($this->cache->resetpwUserId);
                    return $this->redirect()->toUrl("/login");
                }
                throw new Exception\RuntimeException($this->translate("PASSWORD_NOT_GENERATED"));
            } else {
                $this->setLayoutMessages($form->getMessages(), 'error');
                return $this->redirect()->toUrl("/login");
            }
        }
    }

    /**
     * Show the reset password form. After that see if there is a user with the entered email
     * if there is one, send him an email with a new password reset link and a token else show error messages
     */
    public function resetpasswordAction()
    {
        /**
         * @var  Application\Form\ResetPasswordForm
         */
        $form = $this->resetPasswordForm;
        $form->get("resetpw")->setValue($this->translate("RESET_PW"));
        $form->get("email")->setLabel($this->translate("EMAIL"));
        $this->view->form = $form;
        if ($this->getRequest()->isPost()) {
            $form->setInputFilter($form->getInputFilter());
            $form->setData($this->getRequest()->getPost());
            if ($form->isValid()) {
                $formData = $form->getData();
                $existingEmail = $this->getTable("User")->fetchList(false, "email = '".$formData['email']."'")->current();

                if (count($existingEmail) === 1) {
                    $token = Functions::generateToken(48); // returns 64 characters long string
                    $resetpw = new ResetPassword();
                    $remote = new RemoteAddress();
                    $resetpw->setToken($token);
                    $resetpw->setUser($existingEmail->getId());
                    $resetpw->setDate(date("Y-m-d H:i:s", time()));
                    $resetpw->setIp($remote->getIpAddress());
                    $this->getTable("resetpassword")->saveResetPassword($resetpw);

                    $message = $this->translate("NEW_PW_TEXT")." ".$_SERVER["SERVER_NAME"]."/login/newpassword/token/{$token}";
                    $result = Mailing::sendMail($formData['email'], $existingEmail->toString(),  $this->translate("NEW_PW_TITLE"), $message, "noreply@".$_SERVER["SERVER_NAME"], $_SERVER["SERVER_NAME"]);
                    if (!$result) {
                        $this->setLayoutMessages($this->translate("EMAIL_NOT_SENT"), 'error');
                        return $this->redirect()->toUrl("/login/resetpassword");
                    }

                    $this->setLayoutMessages($this->translate("PW_SENT")." ".$formData['email'], 'success');
                    return $this->redirect()->toUrl("/");
                } else {
                    $this->setLayoutMessages($this->translate("EMAIL")." <b>".$formData["email"]."</b> ".$this->translate("NOT_FOUND"), 'warning');
                    return $this->redirect()->toUrl("/login/resetpassword");
                }
            } else {
                $this->setLayoutMessages($form->getMessages(), 'error');
                return $this->redirect()->toUrl("/login");
            }
        }
        return $this->view;
    }

    /**
     * Create new instance of $cache and set it to empty|null
     *
     * Clear all sessions (cache, translations etc.)
     * @param string $redirectTo
     * @return void
     */
    protected function logoutAction($redirectTo = "/")
    {
        $this->cache->getManager()->getStorage()->clear();
        $this->translation->getManager()->getStorage()->clear();
        $this->cache = new Container("cache");
        $this->translation = new Container("translations");
        $authSession = new Container('ul');
        $authSession->getManager()->getStorage()->clear();
        $auth = new AuthenticationService();
        $auth->clearIdentity();
        return $this->redirect()->toUrl($redirectTo);
    }
}
