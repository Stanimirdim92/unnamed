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
 * @category   Admin\Index
 * @package    Unnamed
 * @author     Stanimir Dimitrov <stanimirdim92@gmail.com>
 * @copyright  2015 Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 * @version    0.0.3
 * @link       TBA
 */

namespace Admin\Controller;

use Zend\Session\Container;
use Custom\Error\AuthorizationException;
use Custom\Plugins\Functions;
use Zend\View\Model\ViewModel;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Authentication\AuthenticationService;

class IndexController extends AbstractActionController
{
    /**
     * @var null $cache holds any other session information
     * @return Zend\Session\Container|mixed
     */
    protected $cache = null;

    /**
     * @var null $view creates instance to view model
     * @return Zend\View\Model\ViewModel
     */
    protected $view = null;

    /**
     * @var null $translation holds language data as well as all translations
     * @return Zend\Session\Container
     */
    protected $translation = null;

    /**
     * DRY variable to hold the language. Easier to work with
     *
     * @var null
     * @return int $this->translation->language
     */
    protected $langTranslation = null;

    /**
     * @var array $breadcrumbs returns an array with links with the current user position on the website
     * @return Array
     */
    protected $breadcrumbs = [];

    /**
     * Used to detect actions without IDs. Inherited in all other classes
     */
    const NO_ID = 'Not found';

    /**
     * Query limit
     */
    const MAX_COUNT = 200;

    /**
     * Used when throwing AuthorizationException
     */
    const ACCESS_DENIED = "Access Denied";

    /**
     * constructor
     */
    public function __construct()
    {
        $this->view = new ViewModel();
        $this->initCache();
        $this->initTranslation();
        $this->breadcrumbs[] = ["reference" => "/admin", "name" => "Home"];
    }

    /**
     * Initialize any variables before controller actions
     *
     * @throws Exception\RuntimeException
     * @param \Zend\Mvc\MvcEvent $e
     */
    public function onDispatch(\Zend\Mvc\MvcEvent $e)
    {
        /**
         * @see IndexController::isAdmin
         */
        // $this->isAdmin();
        parent::onDispatch($e);
        $this->initViewVars();
    }

/****************************************************
 * START OF ALL INIT FUNCTIONS
 ****************************************************/

    /**
     * initialize any session variables in this method
     *
     * @return Zend\Session\Container
     */
    private function initCache()
    {
        $this->cache = new Container("cache");
    }

    /**
     * initialize any view related stuff
     */
    private function initViewVars()
    {
        // $this->initMenus();
        if (!isset($this->translation->languageObject)) {
            $this->translation->languageObject = $this->getTable("Language")->getLanguage($this->langTranslation);
            $this->view->langName = $this->translation->languageObject->getName();
        }

        $this->view->breadcrumbs = $this->breadcrumbs;
        $this->view->cache = $this->cache;
        $this->view->translation = $this->translation;
    }

    /**
     * initialize the admin menus
     * @todo  rewrite
     */
    private function initMenus()
    {
        $this->view->adminMenus = $this->getTable("AdminMenu")->fetchList(false, "parent='0' AND advanced='0'", "menuOrder");
        $this->view->advancedMenus = $this->getTable("AdminMenu")->fetchList(false, "parent='0' AND advanced='1'", "menuOrder");
        $this->view->adminsubmenus = $this->getTable("AdminMenu")->fetchList(false, "parent !='0' AND controller='{$this->getParam('__CONTROLLER__')}'", "menuOrder");
    }

    /**
     * initialize languages and language-related stuff like translations.
     */
    private function initTranslation()
    {
        $this->translation = new Container("translations");

        /**
         * Load English as default language.
         * Maybe make this possible to change via backend?
         */
        if (!isset($this->translation->language)) {
            $this->translation = Functions::initTranslations(1, true);
            $this->translation->language = 1;
        }

        // keeping it simple and DRY
        $this->langTranslation = ((int) $this->translation->language > 0 ? $this->translation->language : 1);
    }

/****************************************************
 * START OF ALL MAIN/SHARED FUNCTIONS
 ****************************************************/

    protected function addBreadcrumb(array $breadcrumb = [])
    {
        $this->breadcrumbs[] = $breadcrumb;
    }

    /**
     * Definitely not the best way, but for now I can't think for a better way.
     *
     * @todo  call this via a factory
     * @param null $name
     * @return ObjectTable
     */
    protected function getTable($name = null)
    {
        if (!is_string($name) || empty($name)) {
            throw new \InvalidArgumentException(__METHOD__ . ' must be string and must not be empty');
        }
        return $this->getServiceLocator()->get($name . "Table");
    }

    /**
     * Is the user admin? Lets check that.
     * 1. Run this function and see if we are logged in as admin.
     *    If all went fine show the admin area.
     * 2. Else go to Login Controller and attempt to login as [u]real[/u] admin. Just in case log every access to login controller
     * 3. On success run this function. If all went fine, access admin else clear identity and create log
     *
     * @throws AuthorizationException If wrong credentials or not in administrator table
     * @todo create a bruteforce protection for failed loging attempts.
     * @return void
     */
    private function isAdmin()
    {
        $auth = new AuthenticationService();
        if ($auth->hasIdentity()) {
             if (isset($auth->getIdentity()->role) &&
                ((int) $auth->getIdentity()->role === 10) && isset($auth->getIdentity()->logged) && (bool) $auth->getIdentity()->logged === true) {
                    $checkAdminExistence = $this->getTable("administrator")->fetchList(false, [], ["user" => $auth->getIdentity()->id]);
                    if (count($checkAdminExistence) === 1) {
                        unset($checkAdminExistence);
                        return true;
                    }
                    $this->clearUserData($auth);
            }
            $this->clearUserData($auth);
        }
        $this->clearUserData($auth);
    }

    /**
     * @param AuthenticationService $auth
     * @return void
     * @throws AuthorizationException
     */
    private function clearUserData(AuthenticationService $auth = null)
    {
        $this->cache->getManager()->getStorage()->clear();
        $this->translation->getManager()->getStorage()->clear();
        $auth->clearIdentity();
        $this->cache = null;
        $this->translation = null;
        throw new AuthorizationException($this->translate("ERROR_AUTHORIZATION"));
    }

    /**
     * Shorthand method for getting params from URLs. Makes code easier to modify and avoids DRY code
     *
     * @param String $paramName
     * @param null $default
     * @return mixed
     */
    protected function getParam($paramName = null, $default = null)
    {
        $escaper = new \Zend\Escaper\Escaper('utf-8');
        $param = $this->params()->fromPost($paramName, 0);
        if (!$param) {
            $param = $this->params()->fromRoute($paramName, null);
        }
        if (!$param) {
            $param = $this->params()->fromQuery($paramName, null);
        }
        if (!$param) {
            $param = $this->params()->fromFiles($paramName, null);
        }
        if (!$param) {
            return $default;
        }
        /**
         * If this is array it must comes from fromFiles()
         */
        if (is_array($param)) {
            return $param;
        }
        return $escaper->escapeHtml(trim($param));
    }

    /**
     *
     * @param null|string $message
     * @param null|string $namespace
     */
    protected function setLayoutMessages($message = null, $namespace = 'default')
    {
        $flashMessenger = $this->flashMessenger();
        $messages = [];
        $arrayMessages = [];

        if (!in_array($namespace, ["success", "error", "warning", 'info', 'default'])) {
            $namespace = 'default';
        }

        $flashMessenger->setNamespace($namespace);
        if (is_array($message)) {
            foreach ($message as $msg) {
                if (is_array($msg)) {
                    foreach ($msg as $text) {
                        $flashMessenger->addMessage($text, $namespace);
                    }
                } else {
                    $flashMessenger->addMessage($msg, $namespace);
                }
            }
        } else {
            $flashMessenger->addMessage($message, $namespace);
        }
        $this->view->flashMessages = $flashMessenger;
        return $this->view;
    }

    /**
     * @param  int $code error code
     * @return  ViewModel
     */
    protected function setErrorCode($code = 404)
    {
        $this->getResponse()->setStatusCode($code);
        $this->view->setTemplate('error/index.phtml');
        return $this->view;
    }

    /**
     * Show translated message
     * @param  null|string $str the constant name from the database. It should always be upper case.
     */
    protected function translate($str = null)
    {
        $str = strtoupper($str);
        return (string) $this->translation->{$str};
    }

/****************************************************
 * START OF ALL ACTION METHODS
 ****************************************************/

    public function indexAction()
    {
        return $this->view;
    }
}
