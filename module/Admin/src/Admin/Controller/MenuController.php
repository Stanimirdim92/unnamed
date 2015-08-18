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
 *mits
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
 * @version    0.0.7
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
     * Initialize any variables before controller actions
     *
     * @param MvcEvent $e
     */
    public function onDispatch(\Zend\Mvc\MvcEvent $e)
    {
        parent::onDispatch($e);
        $this->addBreadcrumb(["reference"=>"/admin/menu", "name"=>$this->translate("MENUS")]);
    }

    private function prepareMenusData()
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

    /**
     * This action shows the list with all menus
     */
    public function indexAction()
    {
        $this->view->setTemplate("admin/menu/index");
        $menus = $this->prepareMenusData();
        if (!empty($menus)) {
            $this->view->menus = $menus["menus"];
            $this->view->submenus = $menus["submenus"];
        }
        return $this->view;
    }

    /**
     * This action serves for adding a new menu
     */
    protected function addAction()
    {
        $this->view->setTemplate("admin/menu/add");
        $this->initForm($this->translate("ADD_NEW_MENU"), null);
        $this->addBreadcrumb(["reference"=>"/admin/menu/add", "name"=>$this->translate("ADD_NEW_MENU")]);
        return $this->view;
    }

    /**
     * This action presents a modify form for Menu object with a given id
     * Upon POST the form is processed and saved
     */
    protected function modifyAction()
    {
        $this->view->setTemplate("admin/menu/modify");
        $menu = $this->getTable("menu")->getMenu($this->getParam("id", 0), $this->language())->current();
        $this->view->menu = $menu;
        $this->addBreadcrumb(["reference"=>"/admin/menu/modify/{$menu->getId()}", "name"=> $this->translate("MODIFY_MENU")." &laquo;".$menu->getCaption()."&raquo;"]);
        $this->initForm($this->translate("MODIFY_MENU"), $menu);
        return $this->view;
    }

    /**
     * this action deletes a menu object with a provided id
     */
    protected function deleteAction()
    {
        $this->getTable("menu")->deleteMenu($this->getParam("id", 0), $this->language());
        $this->setLayoutMessages($this->translate("DELETE_MENU_SUCCESS"), "success");
        return $this->redirect()->toRoute('admin/default', ['controller' => 'menu']);
    }

    /**
     * this action shows menu details from the provided id and session language
     */
    protected function detailAction()
    {
        $this->view->setTemplate("admin/menu/detail");
        $menu = $this->getTable("menu")->getMenu($this->getParam("id", 0), $this->language())->current();
        $this->view->menu = $menu;
        $this->addBreadcrumb(["reference"=>"/admin/menu/detail/".$menu->getId()."", "name"=>"&laquo;". $menu->getCaption()."&raquo; ".$this->translate("DETAILS")]);
        return $this->view;
    }

    /**
     * This action will clone the object with the provided id and return to the index view
     */
    protected function cloneAction()
    {
        $menu = $this->getTable("menu")->duplicate($this->getParam("id", 0), $this->language());
        $this->setLayoutMessages("&laquo;".$menu->getCaption()."&raquo; ".$this->translate("CLONE_SUCCESS"), "success");
        return $this->redirect()->toRoute('admin/default', ['controller' => 'menu']);
    }

    /**
     * This is common function used by add and modify actions (to avoid code duplication)
     *
     * @param string $label button title
     * @param  Menu|null $menu menu object
     */
    private function initForm($label = '', Menu $menu = null)
    {
        if (!$menu instanceof Menu) {
            $menu = new Menu([], null);
        }

        /**
         * @var Admin\Form\MenuForm $form
         */
        $form = $this->menuForm;
        $form->bind($menu);
        $form->get("submit")->setValue($label);
        $this->view->form = $form;

        if ($this->getRequest()->isPost()) {
            $form->setInputFilter($form->getInputFilter());
            $form->setData($this->getRequest()->getPost());
            if ($form->isValid()) {
                $formData = $form->getData();
                // see if we have menu with the exact same caption.
                if ($this->getParam("action") == 'add') {
                    $existingMenu = $this->getTable('menu')->fetchList(false, ['menulink', 'menutype', 'language', 'parent'], ["parent" => 0, "language" => $this->language(), "menutype" => $formData->menutype, "menulink" => $formData->menulink], "AND", null);
                    if (count($existingMenu) > 0) {
                        $this->setLayoutMessages($this->translate("MENU_WITH_NAME")." &laquo; ".$formData->caption." &raquo; ".$this->translate("ALREADY_EXIST"), 'warning');
                        return $this->redirect()->toRoute('admin/default', ['controller' => 'menu']);
                    }
                }
                $this->getTable("menu")->saveMenu($menu);
                $this->setLayoutMessages("&laquo;".$menu->getCaption()."&raquo; ".$this->translate("SAVE_SUCCESS"), 'success');
            } else {
                $this->setLayoutMessages($form->getMessages(), 'error');
            }
            return $this->redirect()->toRoute('admin/default', ['controller' => 'menu']);
        }
    }
}
