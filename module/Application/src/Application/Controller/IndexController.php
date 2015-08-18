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
 * @version    0.0.7
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
            $this->view->identity = $userData->getIdentity();
        }

        parent::onDispatch($e);
        $this->initMenus();

        /**
         * Call this method only if we are not in Menu or News. Both of them calls the function by themselves
         */
        if ($this->params('controller') != "Application\Controller\Menu" && $this->params('controller') != "Application\Controller\News") {
            $this->initMetaTags();
        }
    }

/****************************************************
 * START OF ALL INIT FUNCTIONS
 ****************************************************/

    /**
     * Initialize menus and their submenus. 1 query to rule them all!
     *
     * First get all menus.
     * Second, itterate over each object and determinate if it's a submenu or not
     * Third separate each object based on it's type and prepare it for the view itteration
     *
     * @todo  make it dinamicly multilevel
     * @return void
     */
    private function initMenus()
    {
        $menu = $this->getTable("Menu")->fetchList(false, ["id", "caption", "menulink", "parent"], ["language" => $this->language()], "AND", null, "id, menuOrder");
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
            $this->view->menus = $menus["menus"];
            $this->view->submenus = $menus["submenus"];
        }
        return $this->view;
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
        $this->view->setTemplate("application/index/index");
        return $this->view;
    }

    /**
     * Select new language
     *
     * This will reload the translations every time the method is being called
     */
    protected function languageAction()
    {
        $id = $this->getParam("id", 1);
        $language = $this->getTable("language")->getLanguage($id);

        $this->translation->language = $language->getId();
        $this->translation->languageName = $language->getName();

        return $this->redirect()->toUrl("/");
    }
}
