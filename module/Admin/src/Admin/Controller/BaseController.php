<?php

/**
 * @copyright  2015 (c) Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 *
 * @version    0.0.21
 *
 * @link       TBA
 */

namespace Admin\Controller;

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

    /**
     * @var array $breadcrumbs returns an array with links with the current user position on the website
     */
    private $breadcrumbs = [];

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
     *
     * @return mixed|void
     */
    public function onDispatch(MvcEvent $event)
    {
        if (APP_ENV !== 'development') {
            $this->isAdmin();
        }

        parent::onDispatch($event);
        $this->initMenus();

        $this->getView()->breadcrumbs = $this->breadcrumbs;
    }

    /**
     * Initialize menus and their submenus. 1 query to rule them all!
     *
     * @return \Zend\View\Model\ViewModel
     */
    private function initMenus()
    {
        $menu = $this->getTable("Admin\\Model\\AdminMenuTable")
                     ->getEntityRepository()
                     ->findAll();

        if (count($menu) > 0) {
            $menus = ['menus' => [], 'submenus' => []];
            foreach ($menu as $submenus) {
                $menus['menus'][$submenus->getId()] = $submenus;
                $menus['submenus'][$submenus->getParent()][] = $submenus->getId();
            }

            $output = "<li role='menuitem'><a hreflang='{$this->language("languageName")}' itemprop='url' href='&sol;admin'> {$this->translate("DASHBOARD")}</a></li>";

            $this->getView()->menuAdmin = $this->generateMenu(0, $menus, "menubar", $output);
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
    private function generateMenu($parent = 0, array $menu = [], $role = "menubar", $html ='')
    {
        $output = "";
        if (isset($menu["submenus"][$parent])) {
            $output .= "<ul role='{$role}'>";
            $output .= $html;

            foreach ($menu['submenus'][$parent] as $id) {
                $output .= "<li role='menuitem'><a hreflang='{$this->language("languageName")}' itemprop='url' href='/admin/{$menu['menus'][$id]->getController()}/{$menu['menus'][$id]->getAction()}'><em class='fa {$menu['menus'][$id]->getClass()}'></em> {$menu['menus'][$id]->getCaption()}</a>";
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
     * @param array $breadcrumbs
     */
    final protected function addBreadcrumb(array $breadcrumb = [])
    {
        $this->breadcrumbs[] = $breadcrumb;
    }

    /**
     * Is the user admin? Lets check that.
     * 1. Run this function and see if we are logged in as admin.
     *    If all went fine show the admin area.
     * 2. Else go to Login Controller and attempt to login as [u]real[/u] admin. Just in case log every access to login controller.
     * 3. On success run this function. If all went fine, access admin else clear identity and create log.
     *
     * @throws \Admin\Exception\AuthorizationException If wrong credentials or not in administrator table
     *
     * @todo create a bruteforce protection for failed login attempts.
     *
     * @return mixed
     */
    private function isAdmin()
    {
        $auth = $this->UserData();
        if ($auth->checkIdentity(false, $this->translate("ERROR_AUTHORIZATION"))) {
            $userId = $auth->getIdentity()["id"];
            $adminExist = $this->getTable("Admin\\Model\\AdministratorTable")
                                        ->queryBuilder()
                                        ->getEntityManager()
                                        ->createQuery("SELECT a.user, u.name FROM Admin\Entity\Administrator AS a LEFT JOIN Admin\Entity\User AS u WITH a.user=u.id WHERE u.id = {$userId} AND u.admin = 1")->getResult();

            if (isset($adminExist[0])) {
                unset($adminExist);
                return true;
            }
        }

        return $auth->clearUserData($this->translate("ERROR_AUTHORIZATION"));
    }

    /**
     * @return ViewModel
     */
    final protected function getView()
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
