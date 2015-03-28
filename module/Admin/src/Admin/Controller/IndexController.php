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
 * @package    ZendPress
 * @author     Stanimir Dimitrov <stanimirdim92@gmail.com>
 * @copyright  2015 Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 * @version    0.03
 * @link       TBA
 */

namespace Admin\Controller;

use Zend\Session\Container;

use Custom\Error\AuthorizationException;
use Custom\Plugins\Functions;

class IndexController extends \Zend\Mvc\Controller\AbstractActionController
{
    /**
     * @var null $cache holds any other session information, contains warning, success and error vars that are shown just once and then reset
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
    protected $breadcrumbs = array();

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
        $this->view = new \Zend\View\Model\ViewModel();
        $this->initCache();
        $this->initTranslation();
        $this->breadcrumbs[] = array("reference" => "/admin", "name" => "Home");
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
         * Check admin status, before anything else
         */
        // $this->initAdminIdentity();
        parent::onDispatch($e);
        $this->initViewVars();
        $this->initBreadcrumbs();
        $this->initMenus();
    }

/****************************************************
 * START OF ALL INIT FUNCTIONS
 ****************************************************/
    /**
     * initialize breadcrumbs
     * @return  array
     */
    private function initBreadcrumbs()
    {
        $this->view->breadcrumbs = $this->breadcrumbs;
    }

    /**
     * initialize any session variables in this method
     *
     * @return Zend\Session\Container
     */
    private function initCache()
    {
        if (!$this->cache)
        {
            $this->cache = new Container("cache");
            $this->view->cache = $this->cache;
        }
    }

    /**
     * initialize any view related stuff
     */
    private function initViewVars()
    {
        $this->view->translation = $this->translation;
        $this->view->languages = $this->getTable("Language")->fetchList(false, array(), array("active" => 1), "AND", null, "name ASC");
        $this->view->controller = $this->getParam('__CONTROLLER__');
        $this->view->action = $this->getParam('action');
    }

    /**
     * initialize the admin menus
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
        if(!isset($this->translation->language))
        {
            $this->translation = Functions::initTranslations(1, true);
            $this->translation->language = 1;
        }
        // keeping it simple and DRY
        $this->langTranslation = ((int) $this->translation->language > 0 ? $this->translation->language : 1);
    }

/****************************************************
 * START OF ALL MAIN/SHARED FUNCTIONS
 ****************************************************/

    protected function addBreadcrumb(array $breadcrumb = array())
    {
        $this->breadcrumbs[] = $breadcrumb;
    }

    /**
     * @param String $name
     * @return Ambigous <object, multitype:>
     */
    protected function getTable($name = null)
    {
        if (!is_string($name) || !$name)
        {
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
    private function initAdminIdentity()
    {
        $auth = new \Zend\Authentication\AuthenticationService();
        if($auth->hasIdentity() && $this->cache->admin instanceof \Admin\Model\User)
        {
            if($auth->getIdentity()->role === 10 && $this->cache->role === 10 && $this->cache->logged === true)
            {
                $checkAdminExistence = $this->getTable("administrator")->fetchList(false, array(), array("user" => $auth->getIdentity()->id));
                if (count($checkAdminExistence) === 1)
                {
                    return $this->redirect()->toUrl("/admin");
                }
                unset($checkAdminExistence);
                $this->clearUserData();
                return $this->redirect()->toUrl("/");
            }
            $this->clearUserData();
            return $this->redirect()->toUrl("/");
        }
        $this->clearUserData();
        return $this->redirect()->toUrl("/");
    }

    /**
     * clear user data - sessions, cache etc
     * TODO maybe include a logging function
     *
     * @return void
     * @throws AuthorizationException
     */
    private function clearUserData()
    {
        $this->cache->getManager()->getStorage()->clear();
        $this->translation->getManager()->getStorage()->clear();
        $this->cache = new Container("cache");
        $this->translation = new Container("translations");
        $authSession = new Container('ul');
        $authSession->getManager()->getStorage()->clear();
        throw new AuthorizationException($this->translation->ERROR_AUTHORIZATION);
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
        $param = $this->params()->fromPost($paramName, 0);
        if(!$param)
        {
            $param = $this->params()->fromRoute($paramName, null);
        }
        if(!$param)
        {
            $param = $this->params()->fromQuery($paramName, null);
        }
        if(!$param)
        {
            return $default;
        }
       return trim($param);
    }

    /**
     * @param null $message holds the generated error(s)
     * @return string|array
     */
    protected function setErrorNoParam($message = null)
    {
        if(!empty($message))
        {
            $this->cache->error = $message;
        }
        else if ($message === self::NO_ID)
        {
            $this->cache->error = $this->translation->NO_ID_SET;
        }
        else
        {
            $this->cache->error = $this->translation->ERROR_STRING;
        }
        $this->view->setTerminal(true);
    }

    protected function setErrorCode($code = 404)
    {
        $this->getResponse()->setStatusCode($code);
        $this->view->setTemplate('layout/error-layout');
        return $this->view;
    }

/****************************************************
 * START OF ALL ACTION METHODS
 ****************************************************/

    public function indexAction()
    {
        return $this->view;
    }
}

?>