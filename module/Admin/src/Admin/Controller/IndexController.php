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
 * @copyright  2015 (c) Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 * @version    0.0.4
 * @link       TBA
 */

namespace Admin\Controller;

use Zend\Session\Container;
use Zend\Authentication\AuthenticationService;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Custom\Error\AuthorizationException;

class IndexController extends AbstractActionController
{
    /**
     * @var Zend\View\Model\ViewModel $view creates instance to view model
     */
    protected $view = null;

    /**
     * @var Zend\Session\Container $translation holds language data as well as all translations
     */
    protected $translation = null;

    /**
     * @var array $breadcrumbs returns an array with links with the current user position on the website
     */
    private $breadcrumbs = [];

    public function __construct()
    {
        $this->view = new ViewModel();
        $this->translation = new Container("zpc");
        $this->breadcrumbs[] = ["reference" => "/admin", "name" => $this->translate("DASHBOARD")];
    }

    /**
     * Initialize any variables before controller actions
     *
     * @param \Zend\Mvc\MvcEvent $e
     * @method  IndexController::isAdmin()
     */
    public function onDispatch(MvcEvent $e)
    {
        if (APP_ENV !== 'development') {
            /**
             * @see IndexController::isAdmin()
             */
            $this->isAdmin();
        }
        parent::onDispatch($e);
        $this->initTranslation();
    }

/****************************************************
 * START OF ALL INIT FUNCTIONS
 ****************************************************/

    /**
     * initialize the admin menus
     * @todo  rewrite
     */
    // private function initMenus()
    // {
    //     $this->view->adminMenus = $this->getTable("AdminMenu")->fetchList(false, "parent='0' AND advanced='0'", "menuOrder");
    //     $this->view->advancedMenus = $this->getTable("AdminMenu")->fetchList(false, "parent='0' AND advanced='1'", "menuOrder");
    //     $this->view->adminsubmenus = $this->getTable("AdminMenu")->fetchList(false, "parent !='0' AND controller='{$this->getParam('__CONTROLLER__')}'", "menuOrder");
    // }

    /**
     * Initialize translations.
     *
     * @return  void
     */
    private function initTranslation()
    {
        if (empty($this->translation->language)) {
            /**
             * Load English as default language.
             */
            // $terms = $this->getTable("term")->fetchJoin(false, "termtranslation", ["name"], ["translation"], "term.id=termtranslation.term", "inner", ["termtranslation.language" => $this->language()])->getDataSource();

            // foreach ($terms as $term) {
            //     $this->translation->$term['name'] = $term['translation'];
            // }
            $this->translation->language = 1;
        }
    }

/****************************************************
 * START OF ALL MAIN/SHARED FUNCTIONS
 ****************************************************/

    /**
     * @param array $breadcrumbs
     * @return  ViewModel
     */
    protected function addBreadcrumb(array $breadcrumb = [])
    {
        $this->breadcrumbs[] = $breadcrumb;
        $this->view->breadcrumbs = $this->breadcrumbs;
        return $this->view;
    }

    /**
     * @param null $name
     * @return object
     */
    protected function getTable($name = null)
    {
        $plugin = $this->IndexPlugin();
        return $plugin->getTable($name);
    }

    /**
     * Is the user admin? Lets check that.
     * 1. Run this function and see if we are logged in as admin.
     *    If all went fine show the admin area.
     * 2. Else go to Login Controller and attempt to login as [u]real[/u] admin. Just in case log every access to login controller
     * 3. On success run this function. If all went fine, access admin else clear identity and create log
     *
     * @throws AuthorizationException If wrong credentials or not in administrator table
     * @todo create a bruteforce protection for failed login attempts.
     * @todo create a join query for admin column check via the user table.
     * @return mixed
     */
    private function isAdmin()
    {
        $auth = new AuthenticationService();
        if ($auth->hasIdentity()) {
            if (isset($auth->getIdentity()->role) &&
                ((int) $auth->getIdentity()->role === 10) && isset($auth->getIdentity()->logged) && $auth->getIdentity()->logged === true) {
                $checkAdminExistence = $this->getTable("administrator")->fetchList(false, [], ["user" => $auth->getIdentity()->id]);
                if (count($checkAdminExistence) === 1) {
                    unset($checkAdminExistence);
                    return true;
                }
                return $this->clearUserData($auth);
            }
            return $this->clearUserData($auth);
        }
        return $this->clearUserData($auth);
    }

    /**
     * @param AuthenticationService $auth
     * @return void
     * @throws AuthorizationException
     */
    private function clearUserData(AuthenticationService $auth = null)
    {
        $this->translation->getManager()->getStorage()->clear();
        $auth->clearIdentity();
        $this->translation = new Container("zpc");
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
        /**
         * If this is array it must comes from fromFiles()
         */
        if (is_array($param) && !empty($param)) {
            return $param;
        }
        if (!$param) {
            return $default;
        }
        return $escaper->escapeHtml(trim($param));
    }

    /**
     * This method will iterate over an array and show its contents as separated strings
     * The method will accept an array with unlimited depth.
     *
     * <code>
     *     $myArray = [
     *         0 => 'A',
     *         1 => ['subA','subB',
     *                  [0 => 'subsubA', 1 => 'subsubB',
     *                      2 => [0 => 'subsubsubA', 1 => 'subsubsubB']
     *                  ]
     *              ],
     *         2 => 'B',
     *         3 => ['subA','subB','subC'],
     *         4 => 'C'
     *     ];
     *     $this->setLayoutMessages($myArray, "default");
     * </code>
     *
     * @param array|arrayobject|string $message
     * @param string $namespace determinates the message layout and color. It's also used for the flashMessenger namespace
     * @return ViewModel
     */
    protected function setLayoutMessages($message = [], $namespace = 'default')
    {
        $flashMessenger = $this->flashMessenger();

        if (!in_array($namespace, ["success", "error", "warning", 'info', 'default'])) {
            $namespace = 'default';
        }

        $flashMessenger->setNamespace($namespace);

        $iterator = new \RecursiveArrayIterator((array) $message);

        while ($iterator->valid()) {
            if ($iterator->hasChildren()) {
                $this->setLayoutMessages($iterator->getChildren(), $namespace);
            } else {
                $flashMessenger->addMessage($iterator->current(), $namespace);
            }
            $iterator->next();
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
        $this->view->setVariables([
            'message' => '404 Not found',
            'reason' => 'The link you have requested doesn\'t exists',
            'exception' => "",
        ]);
        $this->view->setTemplate('error/index');
        return $this->view;
    }

    /**
     * Show translated message. ERROR will be used as a default constant.
     * @param string $str the constant name from the database. It should always be upper case.
     * @return string
     */
    public function translate($str = "ERROR")
    {
        if (!empty($this->translation)) {
            /**
             * If the given string / offset doesn't exist
             * the object will automatically return empty string
             */
            return (string) $this->translation->offsetGet($str);
        }
        return "";
    }

    /**
     * Get Language id
     * @return  int
     */
    protected function language()
    {
        if (isset($this->translation->language) && (int) $this->translation->language > 0) {
            return $this->translation->language;
        }
        $this->translation->language = 1;
        return $this->translation->language;
    }

    /**
     * Copy of initMenus() used in Menus|Content controllers
     * @return  array
     */
    private function prepareMenusData()
    {
        $menu = $this->getTable("Menu")->fetchList(false, ['id', 'menulink', 'caption', 'language', 'parent'], ["language" => $this->language()], "AND", null, "id, menuOrder");
        if (count($menu) > 0) {
            $menus = ["menus" => null, "submenus" => null];
            foreach ($menu->getDataSource() as $submenu) {
                if ($submenu->getParent() > 0) {
                    /**
                     * This needs to have a second empty array in order to work
                     */
                    $menus["submenus"][$submenu->getParent()][] = $submenu;
                } else {
                    $menus["menus"][$submenu->getId()] = $submenu;
                }
            }
            return $menus;
        }
        return [];
    }

/****************************************************
 * START OF ALL ACTION METHODS
 ****************************************************/

    /**
     * @return  ViewModel
     */
    public function indexAction()
    {
        $this->view->setTemplate("admin/index/index");
        return $this->view;
    }

    /**
     * Select new language
     *
     * This will reload the translations every time the method is being called
     */
    public function languageAction()
    {
        $terms = $this->getTable("term")->fetchList(false, ["name", "translation"], ["language" => (int) $this->getParam("id")]);

        if ($terms) {
            foreach ($terms as $term) {
                $this->translation->offsetSet($term['name'], $term['translation']);
            }
            $this->translation->language = (int) $this->getParam("id");
        }
        return $this->redirect()->toUrl("/");
    }
}
