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
 * CLAIM, DAMAGES OR OTHER LIABILITY, WHE`HER IN AN ACTION OF CONTRACT,
 * TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE
 * SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 *
 * @author     Stanimir Dimitrov <stanimirdim92@gmail.com>
 * @copyright  2015 (c) Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 * @version    0.0.10
 * @link       TBA
 */

namespace Application\Controller;

use Zend\Session\Container;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class IndexController extends AbstractActionController
{
    /**
     * @var ViewModel $view creates instance to view model
     */
    protected $view = null;

    /**
     * @var $menuIncrementHack Used increment the menu and stop the second show up of home, login and logout links...
     */
    private $menuIncrementHack = 0;

    /**
     * @var Container $translation holds language id and name
     */
    protected $translation = null;

    public function __construct()
    {
        $this->view = new ViewModel();
        $this->translation = new Container("translations");

        if (!$this->translation->offSetExists("language")) {
            $this->translation->language = 1;
            $this->translation->languageName = "en";
        }
    }

    /**
     * Initialize any variables before controller actions
     *
     * @param MvcEvent $e
     */
    public function onDispatch(MvcEvent $e)
    {
        $userData = $this->UserData();
        if ($userData->checkIdentity(false)) {
            $this->getView()->identity = $userData->getIdentity();
        }

        parent::onDispatch($e);
        $this->initMenus();

        /**
         * Call this method only if we are not in Menu or News. Both of them calls the function by themselves
         */
        if ($this->params('action') != "title" || $this->params('action') != "post") {
            $this->initMetaTags();
        }

        return $this->getView();
    }

/****************************************************
 * START OF ALL INIT FUNCTIONS
 ****************************************************/

    /**
     * Initialize menus and their submenus. 1 query to rule them all!
     *
     * @return void
     */
    private function initMenus()
    {
        $menu = $this->getTable("Menu")->fetchList(false, ["id", "caption", "menulink", "parent"], ["active" => 1, "language" => $this->language()], "AND", null, "id, menuOrder")->getDataSource();
        if (count($menu) > 0) {
            $menus = ['menus' => [], 'submenus' => []];

            foreach ($menu as $submenus) {
                $menus['menus'][$submenus['id']] = $submenus;
                $menus['submenus'][$submenus['parent']][] = $submenus['id'];
            }

            $this->getView()->menu = $this->generateMenu(0, $menus);
        }
        return $this->getView();
    }

    private function generateMenu($parent, $menu)
    {
        $output = "";
        if (isset($menu["submenus"][$parent])) {
            $output .= "<ul>";

            /**
             * This is a really, really ugly hack
             */
            if ($this->menuIncrementHack === 0) {
                $output .= "<li><a hreflang='{$this->language("languageName")}' itemprop='url' href='/'>{$this->translate("HOME")}</a></li>";
                $output .= "<li><a hreflang='{$this->language("languageName")}' itemprop='url' href='/login'>{$this->translate("SIGN_IN")}</a></li>";
                $output .= "<li><a hreflang='{$this->language("languageName")}' itemprop='url' href='/login/logout'>{$this->translate("SIGN_OUT")}</a></li>";
            }
            $this->menuIncrementHack = 1;

            foreach ($menu['submenus'][$parent] as $id) {
                $output .= "<li><a hreflang='{$this->language("languageName")}' itemprop='url' href='/menu/title/{$menu['menus'][$id]['menulink']}'>{$menu['menus'][$id]['caption']}</a>";
                $output .= $this->generateMenu($id, $menu);
                $output .= "</li>";
            }
            $output .= "</ul>";
        }

        return $output;
    }

/****************************************************
 * START OF ALL MAIN/SHARED FUNCTIONS
 ****************************************************/

    /**
     * Get Language id or name. Defaults to language - id
     * If a different offset is passed (not-existing-offset) and it doesn't,
     * it will ty to check for a language offset.
     * If language offset is also not found 1 s being returned as the default language id where 1 == en
     *
     * @return  mixed
     */
    protected function language($offset = "language")
    {
        if ($this->translation->offSetExists($offset)) {
            return $this->translation->offSetGet($offset);
        } elseif ($this->translation->offSetExists("language")) {
            return $this->translation->offSetExists("language");
        }
        return 1;
    }

    /**
     * @return ViewModel
     */
    public function getView()
    {
        return $this->view;
    }

/****************************************************
 * START OF ALL ACTION METHODS
 ****************************************************/

    /**
     * Main websites view
     *
     * @return ViewModel
     */
    public function indexAction()
    {
        $this->getView()->setTemplate("application/index/index");
        return $this->getView();
    }

    /**
     * Select new language
     *
     * This will reload the translations every time the method is being called
     */
    protected function languageAction()
    {
        $language = $this->getTable("language")->getLanguage((int) $this->getParam("id", 1));

        $this->translation->language = $language->getId();
        $this->translation->languageName = $language->getName();

        return $this->redirect()->toUrl("/");
    }
}
