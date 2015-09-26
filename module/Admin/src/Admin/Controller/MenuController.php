<?php

/**
 * @copyright  2015 (c) Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 *
 * @version    0.0.14
 *
 * @link       TBA
 */

namespace Admin\Controller;

use Admin\Model\Menu;
use Admin\Form\MenuForm;

final class MenuController extends IndexController
{
    /**
     * @var MenuForm $menuForm
     */
    private $menuForm = null;

    /**
     * @param MenuForm $menuForm
     */
    public function __construct(MenuForm $menuForm = null)
    {
        parent::__construct();
        $this->menuForm = $menuForm;
    }

    /**
     * Initialize any variables before controller actions.
     *
     * @param MvcEvent $e
     */
    public function onDispatch(\Zend\Mvc\MvcEvent $e)
    {
        parent::onDispatch($e);
        $this->addBreadcrumb(["reference"=>"/admin/menu", "name"=>$this->translate("MENUS")]);
    }

    /**
     * Initialize menus and their submenus. 1 query to rule them all!
     *
     * @return string|null
     */
    private function showMenus()
    {
        $menu = $this->getTable("Menu")->fetchList(false, ['id', 'menulink', 'caption', 'language', 'active', 'parent'], ["language" => $this->language()], "AND", null, "id, menuOrder")->getDataSource();

        if (count($menu) > 0) {
            $menus = ['menus' => [], 'submenus' => []];

            foreach ($menu as $submenus) {
                $menus['menus'][$submenus['id']] = $submenus;
                $menus['submenus'][$submenus['parent']][] = $submenus['id'];
            }

            return $this->generateMenu(0, $menus);
        }
        return null;
    }

    /**
     * Builds menu HTML.
     *
     * @method generateMenu
     *
     * @param int $parent
     * @param array $menu
     *
     * @return string generated html code
     */
    private function generateMenu($parent = 0, array $menu = [])
    {
        $output = "";
        if (isset($menu["submenus"][$parent])) {
            foreach ($menu['submenus'][$parent] as $id) {
                $output .= "<ul class='table-row'>";
                $output .= "<li class='table-cell'>{$menu['menus'][$id]['caption']}</li>";
                $output .= "<li class='table-cell'><a title='{$this->translate('DETAILS')}' hreflang='{$this->language("languageName")}' itemprop='url' href='/admin/menu/detail/{$menu['menus'][$id]['id']}' class='btn btn-sm blue'><i class='fa fa-info'></i></a></li>";
                $output .= "<li class='table-cell'><a title='{$this->translate('MODIFY')}' hreflang='{$this->language("languageName")}' itemprop='url' href='/admin/menu/modify/{$menu['menus'][$id]['id']}' class='btn btn-sm orange'><i class='fa fa-pencil'></i></a></li>";
                if ($menu['menus'][$id]['active'] == 0) {
                    $output .= "<li class='table-cell'><a title='{$this->translate('DEACTIVATED')}' hreflang='{$this->language("languageName")}' itemprop='url' href='/admin/menu/activate/{$menu['menus'][$id]['id']}' class='btn btn-sm deactivated'><i class='fa fa-minus-square-o'></i></a></li>";
                } else {
                    $output .= "<li class='table-cell'><a title='{$this->translate('ACTIVE')}' hreflang='{$this->language("languageName")}' itemprop='url' href='/admin/menu/deactivate/{$menu['menus'][$id]['id']}' class='btn btn-sm active'><i class='fa fa fa-check-square-o'></i></a></li>";
                }
                $output .= "
                <li class='table-cell'>
                    <button id='delete_{$menu['menus'][$id]['id']}' type='button' class='btn btn-sm delete dialog_delete' title='{$this->translate("DELETE")}'><i class='fa fa-trash-o'></i></button>
                        <div id='delete_delete_{$menu['menus'][$id]['id']}' class='dialog_hide'>
                           <p>{$this->translate("DELETE_CONFIRM_TEXT")} &laquo;{$menu['menus'][$id]['caption']}&raquo;</p>
                            <ul>
                                <li>
                                    <a class='btn delete' href='/admin/menu/delete/{$menu['menus'][$id]['id']}'><i class='fa fa-trash-o'></i> {$this->translate("DELETE")}</a>
                                </li>
                                <li>
                                    <a class='btn btn-default cancel'><i class='fa fa-times'></i> {$this->translate("CANCEL")}</a>
                                </li>
                            </ul>
                        </div>
                </li>";

                $output .= $this->generateMenu($id, $menu);
                $output .= "</ul>";
            }
        }

        return $output;
    }

    /**
     * This action shows the list with all menus.
     *
     * @return ViewModel
     */
    public function indexAction()
    {
        $this->getView()->setTemplate("admin/menu/index");

        $menus = $this->showMenus();
        $this->getView()->menus = $menus;

        return $this->getView();
    }

    /**
     * This action serves for adding a new menu
     *
     * @return ViewModel
     */
    protected function addAction()
    {
        $this->getView()->setTemplate("admin/menu/add");
        $this->initForm($this->translate("ADD_NEW_MENU"), null);
        $this->addBreadcrumb(["reference"=>"/admin/menu/add", "name"=>$this->translate("ADD_NEW_MENU")]);
        return $this->getView();
    }

    /**
     * This action presents a modify form for Menu object with a given id.
     * Upon POST the form is processed and saved.
     *
     * @return ViewModel
     */
    protected function modifyAction()
    {
        $this->getView()->setTemplate("admin/menu/modify");
        $menu = $this->getTable("menu")->getMenu((int)$this->getParam("id", 0), $this->language())->current();
        $this->getView()->menu = $menu;
        $this->addBreadcrumb(["reference"=>"/admin/menu/modify/{$menu->getId()}", "name"=> $this->translate("MODIFY_MENU")." &laquo;".$menu->getCaption()."&raquo;"]);
        $this->initForm($this->translate("MODIFY_MENU"), $menu);
        return $this->getView();
    }

    protected function deactivateAction()
    {
        $this->getTable("menu")->toggleActiveMenu((int)$this->getParam("id", 0), 0);
        $this->setLayoutMessages($this->translate("MENU_DISABLE_SUCCESS"), "success");
        return $this->redirect()->toRoute('admin/default', ['controller' => 'menu']);
    }

    protected function activateAction()
    {
        $this->getTable("menu")->toggleActiveMenu((int)$this->getParam("id", 0), 1);
        $this->setLayoutMessages($this->translate("MENU_ENABLE_SUCCESS"), "success");
        return $this->redirect()->toRoute('admin/default', ['controller' => 'menu']);
    }

    /**
     * this action deletes a menu object with a provided id.
     */
    protected function deleteAction()
    {
        $this->getTable("menu")->deleteMenu((int)$this->getParam("id", 0), $this->language());
        $this->setLayoutMessages($this->translate("DELETE_MENU_SUCCESS"), "success");
        return $this->redirect()->toRoute('admin/default', ['controller' => 'menu']);
    }

    /**
     * this action shows menu details from the provided id and session language.
     *
     * @return ViewModel
     */
    protected function detailAction()
    {
        $this->getView()->setTemplate("admin/menu/detail");
        $menu = $this->getTable("menu")->getMenu((int)$this->getParam("id", 0), $this->language())->current();
        $this->getView()->menu = $menu;
        $this->addBreadcrumb(["reference"=>"/admin/menu/detail/".$menu->getId()."", "name"=>"&laquo;". $menu->getCaption()."&raquo; ".$this->translate("DETAILS")]);
        return $this->getView();
    }

    /**
     * This action will clone the object with the provided id and return to the index view.
     */
    protected function cloneAction()
    {
        $menu = $this->getTable("menu")->duplicate((int)$this->getParam("id", 0), $this->language());
        $this->setLayoutMessages("&laquo;".$menu->getCaption()."&raquo; ".$this->translate("CLONE_SUCCESS"), "success");
        return $this->redirect()->toRoute('admin/default', ['controller' => 'menu']);
    }

    /**
     * This is common function used by add and modify actions (to avoid code duplication).
     *
     * @param string $label button title
     * @param Menu $menu menu object
     */
    private function initForm($label = '', Menu $menu = null)
    {
        if (!$menu instanceof Menu) {
            $menu = new Menu([]);
        }

        /**
         * @var MenuForm $form
         */
        $form = $this->menuForm;
        $form->bind($menu);
        $form->get("submit")->setValue($label);
        $this->getView()->form = $form;

        if ($this->getRequest()->isPost()) {
            $form->setInputFilter($form->getInputFilter());
            $form->setData($this->getRequest()->getPost());
            if ($form->isValid()) {
                $formData = $form->getData();
                // see if we have menu with the exact same caption.
                if ((string) $this->getParam("action") === 'add') {
                    $existingMenu = $this->getTable('menu')->fetchList(false, ['menulink', 'menutype', 'language', 'parent'], ["parent" => 0, "language" => $this->language(), "menutype" => $formData->getMenuType(), "menulink" => $formData->getMenuLink()], "AND", null);
                    if (count($existingMenu) > 0) {
                        $this->setLayoutMessages($this->translate("MENU_WITH_NAME")." &laquo; ".$formData->getCaption()." &raquo; ".$this->translate("ALREADY_EXIST"), 'warning');
                        return $this->redirect()->toRoute('admin/default', ['controller' => 'menu']);
                    }
                }
                $this->getTable("menu")->saveMenu($menu);
                $this->setLayoutMessages("&laquo;".$menu->getCaption()."&raquo; ".$this->translate("SAVE_SUCCESS"), 'success');
                return $this->redirect()->toRoute('admin/default', ['controller' => 'menu']);
            } else {
                $this->setLayoutMessages($form->getMessages(), 'error');
            }
        }
    }
}
