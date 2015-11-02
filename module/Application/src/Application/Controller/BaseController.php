<?php

/**
 * @copyright  2015 (c) Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 *
 * @version    0.0.21
 *
 * @link       TBA
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Mvc\MvcEvent;
use Zend\Session\Container;

class BaseController extends AbstractActionController
{
    /**
     * @var ViewModel
     */
    private $view;

    /**
     * @var Container
     */
    private $translation;

    public function __construct()
    {
        $this->view = new ViewModel();
        $this->translation = new Container("translations");

        if (!$this->getTranslation()->offSetExists("language")) {
            $this->getTranslation()->offsetSet("language", 1);
            $this->getTranslation()->offsetSet("languageName", "en");
        }
    }

    /**
     * @param MvcEvent $event
     */
    public function onDispatch(MvcEvent $event)
    {
        $userData = $this->UserData();
        if ($userData->checkIdentity(false)) {
            $this->getView()->identity = $userData->getIdentity();
        }

        parent::onDispatch($event);
        $this->initMenus();

        /*
         * Call this method only if we are not in Menu or News. Both of them calls the function by themselves
         */
        if (($this->params('action') != "post")) {
            $this->initMetaTags();
        }
    }

    /**
     * Initialize menus and their submenus. 1 query to rule them all!
     *
     * @return ViewModel
     */
    private function initMenus()
    {
        $menu = $this->getTable("Admin\\Model\\MenuTable")
                     ->getEntityRepository()
                     ->findBy(["active" => 1, "language" => $this->language()], ['parent' => "DESC"]);

        if (count($menu) > 0) {
            $menus = ['menus' => [], 'submenus' => []];
            foreach ($menu as $submenus) {
                $menus['menus'][$submenus->getId()] = $submenus;
                $menus['submenus'][$submenus->getParent()][] = $submenus->getId();
            }

            $output = "<li role='menuitem'><a hreflang='{$this->language("languageName")}' itemprop='url' href='/'>{$this->translate("HOME")}</a></li>";
            $output .= "<li role='menuitem'><a hreflang='{$this->language("languageName")}' itemprop='url' href='/news'>{$this->translate("NEWS")}</a></li>";
            if ($this->UserData()->checkIdentity(false)) {
                $output .= "<li role='menuitem'><a hreflang='{$this->language("languageName")}' itemprop='url' href='/login/logout'>{$this->translate("SIGN_OUT")}</a></li>";
            } else {
                $output .= "<li role='menuitem'><a hreflang='{$this->language("languageName")}' itemprop='url' href='/login'>{$this->translate("SIGN_IN")}</a></li>";
                $output .= "<li role='menuitem'><a hreflang='{$this->language("languageName")}' itemprop='url' href='/registration'>{$this->translate("SIGN_UP")}</a></li>";
            }

            $this->getView()->menu = $this->generateMenu(0, $menus, "menubar", $output);
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
     * @param string $html - add html menus that do not come from database
     *
     * @return string generated html code
     */
    private function generateMenu($parent = 0, array $menu = [], $ariaRole = "menubar", $html = '')
    {
        $output = "";
        if (isset($menu["submenus"][$parent])) {
            $output .= "<ul role='{$ariaRole}'>";
            $output .= $html;

            foreach ($menu['submenus'][$parent] as $id) {
                $output .= "<li role='menuitem'><a hreflang='{$this->language("languageName")}' itemprop='url' href='/menu/post/{$menu['menus'][$id]->getMenuLink()}'><em class='fa {$menu['menus'][$id]->getClass()}'></em> {$menu['menus'][$id]->getCaption()}</a>";
                $output .= $this->generateMenu($id, $menu, "menu");
                $output .= "</li>";
            }
            $output .= "</ul>";
        }

        return $output;
    }

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
        if ($this->getTranslation()->offSetExists($offset)) {
            return $this->getTranslation()->offSetGet($offset);
        } elseif ($this->getTranslation()->offSetExists("language")) {
            return $this->getTranslation()->offSetGet("language");
        } else {
            return 1;
        }
    }

    /**
     * @method getView
     *
     * @return ViewModel
     */
    protected function getView()
    {
        return $this->view;
    }

    /**
     * Returns session holding translations id and name
     *
     * @method getTranslation
     *
     * @return Container
     */
    protected function getTranslation()
    {
        return $this->translation;
    }
}
