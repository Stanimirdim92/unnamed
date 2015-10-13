<?php

/**
 * @copyright  2015 (c) Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 *
 * @version    0.0.18
 *
 * @link       TBA
 */

namespace Application\Controller;

use Zend\Session\Container;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Escaper\Escaper;

class IndexController extends AbstractActionController
{
    /**
     * @var ViewModel $view creates instance to view model
     */
    private $view = null;

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
     * @param MvcEvent $e
     *
     */
    public function onDispatch(MvcEvent $e)
    {
        $userData = $this->UserData();
        if ($userData->checkIdentity(false)) {
            $this->getView()->identity = $userData->getIdentity();
        }

        parent::onDispatch($e);
        $this->initMenus();

        /*
         * Call this method only if we are not in Menu or News. Both of them calls the function by themselves
         */
        if (($this->params('action') != "post")) {
            $this->initMetaTags();
        }
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
        $menu = $this->getTable("MenuTable");
        $menu->columns(["id", "caption", "class", "menulink", "parent"]);
        $menu->where(["active" => 1, "language" => $this->language()], "AND");
        $menu->order("id, menuOrder");
        $menu = $menu->fetch();

        if (count($menu) > 0) {
            $menus = ['menus' => [], 'submenus' => []];
            foreach ($menu as $submenus) {
                $menus['menus'][$submenus->getId()] = $submenus;
                $menus['submenus'][$submenus->getParent()][] = $submenus->getId();
            }

            $this->getView()->menu = $this->generateMenu(0, $menus);
        }
        return $this->getView();
    }

    /**
     * Builds menu HTML.
     *
     * @method generateMenu
     *
     * @param int $parent
     * @param array $menu
     * @param string $role
     *
     * @return string generated html code
     */
    private function generateMenu($parent = 0, array $menu = [], $ariaRole = "menubar")
    {
        $output = "";
        $escaper = new Escaper('utf-8');
        if (isset($menu["submenus"][$parent])) {
            $output .= "<ul role='{$ariaRole}'>";

            /**
             * This is a really, really ugly hack
             */
            if ($this->menuIncrementHack === 0) {
                $output .= "<li role='menuitem'><a hreflang='{$this->language("languageName")}' itemprop='url' href='/'>{$this->translate("HOME")}</a></li>";
                $userData = $this->UserData();
                if ($userData->checkIdentity(false)) {
                    $output .= "<li role='menuitem'><a hreflang='{$this->language("languageName")}' itemprop='url' href='/login/logout'>{$this->translate("SIGN_OUT")}</a></li>";
                } else {
                    $output .= "<li role='menuitem'><a hreflang='{$this->language("languageName")}' itemprop='url' href='/login'>{$this->translate("SIGN_IN")}</a></li>";
                }
                $output .= "<li role='menuitem'><a hreflang='{$this->language("languageName")}' itemprop='url' href='/registration'>{$this->translate("SIGN_UP")}</a></li>";
            }
            $this->menuIncrementHack = 1;

            foreach ($menu['submenus'][$parent] as $id) {
                $output .= "<li role='menuitem'><a hreflang='{$this->language("languageName")}' itemprop='url' href='/menu/post/{$escaper->escapeUrl($menu['menus'][$id]->getMenuLink())}'><em class='fa {$menu['menus'][$id]->getClass()}'></em> {$menu['menus'][$id]->getCaption()}</a>";
                $output .= $this->generateMenu($id, $menu, "menu");
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
     * Get Language id or name. Defaults to language - id.
     * If a different offset is passed (not-existing-offset) and it doesn't,
     * it will ty to check for a language offset.
     * If language offset is also not found 1 s being returned as the default language id where 1 == en.
     *
     * @return mixed
     */
    final protected function language($offset = "language")
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
    final public function getView()
    {
        return $this->view;
    }

/****************************************************
 * START OF ALL ACTION METHODS
 ****************************************************/

    /**
     * Main websites view.
     *
     * @return ViewModel
     */
    public function indexAction()
    {
        $this->getView()->setTemplate("application/index/index");

        return $this->getView();
    }

    /**
     * Select new language.
     *
     * This will reload the translations every time the method is being called.
     */
    final protected function languageAction()
    {
        $language = $this->getTable("LanguageTable")->getLanguage((int) $this->getParam("id", 1));

        $this->translation->language = $language->getId();
        $this->translation->languageName = $language->getName();

        return $this->redirect()->toUrl("/");
    }
}
