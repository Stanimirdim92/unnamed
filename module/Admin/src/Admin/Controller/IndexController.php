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
 * @version    0.0.6
 * @link       TBA
 */

namespace Admin\Controller;

use Zend\Session\Container;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class IndexController extends AbstractActionController
{
    /**
     * @var Zend\View\Model\ViewModel $view creates instance to view model
     */
    protected $view = null;

    /**
     * @var array $translation holds language data as well as all translations
     */
    protected $translation = [];

    /**
     * @var array $breadcrumbs returns an array with links with the current user position on the website
     */
    private $breadcrumbs = [];

    public function __construct()
    {
        $this->view = new ViewModel();
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
        $this->view->breadcrumbs = $this->breadcrumbs;
    }

/****************************************************
 * START OF ALL INIT FUNCTIONS
 ****************************************************/


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
        $auth = $this->UserData();
        if ($auth->checkIdentity(false, $this->translate("ERROR_AUTHORIZATION"))) {
            if (
                isset($auth->getIdentity()->role)         &&
                ((int) $auth->getIdentity()->role === 10) &&
                isset($auth->getIdentity()->logged)       &&
                $auth->getIdentity()->logged === true
            ) {
                $checkAdminExistence = $this->getTable("administrator")->fetchList(false, [], ["user" => $auth->getIdentity()->id])->current();
                if (count($checkAdminExistence) === 1) {
                    unset($checkAdminExistence);
                    return true;
                }
                return $auth->clearUserData($this->translate("ERROR_AUTHORIZATION"));
            }
            return $auth->clearUserData($this->translate("ERROR_AUTHORIZATION"));
        }
        return $auth->clearUserData($this->translate("ERROR_AUTHORIZATION"));
    }

    /**
     * Copy of initMenus() used in Menus|Content controllers
     *
     * @return array
     */
    protected function prepareMenusData()
    {
        $menu = $this->getTable("Menu")->fetchList(false, ['id', 'menulink', 'caption', 'language', 'parent'], ["language" => $this->language()], "AND", null, "id, menuOrder");
        if (count($menu) > 0) {
            $menus = ["menus" => null, "submenus" => null];
            foreach ($menu as $submenu) {
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
        // if (empty($this->translation["language"])) {
            /**
             * Load English as default language.
             */
            // $terms = $this->getTable("term")->fetchJoin(false, "termtranslation", ["name"], ["translation"], "term.id=termtranslation.term", "inner", ["termtranslation.language" => $this->language()])->getDataSource();

            // foreach ($terms as $term) {
            //     $this->translation->$term['name'] = $term['translation'];
            // }
            // $this->translation["language"] = 1;
        // }
    }

    /**
     * Select new language
     *
     * This will reload the translations every time the method is being called
     * @todo implement logic
     */
    protected function languageAction()
    {
        return $this->redirect()->toUrl("/");
    }


    /**
     * @todo implement logic
     */
    public function translate($a)
    {
        return $a;
    }

    /**
     * Get Language id
     * @return  int
     * @todo implement logic
     */
    protected function language()
    {
        // if (isset($this->translation["language"]) && (int) $this->translation["language"] > 0) {
        //     return $this->translation["language"];
        // }
        $this->translation["language"] = 1;
        return $this->translation["language"];
    }
}
