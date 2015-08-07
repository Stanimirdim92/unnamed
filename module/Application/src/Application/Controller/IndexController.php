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
 * @version    0.0.5
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
     * @var Zend\View\Model\ViewModel $view creates instance to view model
     */
    protected $view = null;

    /**
     * @var array $translation holds language data as well as all translations
     */
    protected $translation = [];

    public function __construct()
    {
        $this->view = new ViewModel();
    }

    /**
     * Initialize any variables before controller actions
     *
     * @param \Zend\Mvc\MvcEvent $e
     */
    public function onDispatch(MvcEvent $e)
    {
        $userData = $this->UserData();
        if ($userData->checkIdentity(false, $this->translate("ERROR_AUTHORIZATION"))) {
            $this->view->identity = $userData->getIdentity();
        }

        parent::onDispatch($e);
        // $this->initTranslation();
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
     * Initialize translations.
     *
     * @return  void
     */
    private function initTranslation()
    {
        // if (!isset($this->translation["language"])) {
            /**
             * Load English as default language.
             */
            // $terms = $this->getTable("term")->fetchJoin(false, "termtranslation", ["name"], ["translation"], "term.id=termtranslation.term", "inner", ["termtranslation.language" => $this->language()])->getDataSource();

            // foreach ($terms as $term) {
            //     $this->translation[$term['name']] = $term['translation'];
            // }
            // echo \Zend\Debug\Debug::dump($this->translation, null, false);
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
