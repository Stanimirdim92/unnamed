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
 * @version    0.0.4
 * @link       TBA
 */

namespace Admin\Controller;

use Admin\Model\AdminMenu;
use Admin\Form\AdminMenuForm;

class AdminMenuController extends IndexController
{
    /**
     * @var Admin\Form\AdminMenuForm $adminMenuForm
     */
    private $adminMenuForm = null;

    /**
     * @param Admin\Form\AdminMenuForm $adminMenuForm
     */
    public function __construct(AdminMenuForm $adminMenuForm = null)
    {
        parent::__construct();

        $this->adminMenuForm = $adminMenuForm;
    }

    /**
     * Initialize any variables before controller actions
     *
     * @param MvcEvent $e
     */
    public function onDispatch(\Zend\Mvc\MvcEvent $e)
    {
        $this->addBreadcrumb(["reference"=>"/admin/adminmenu", "name"=>$this->translate("ADMIN_MENUS")]);
        parent::onDispatch($e);
    }

    /**
     * This action shows the list of all admin menus
     */
    public function indexAction()
    {
        $this->view->setTemplate("admin/admin-menu/index");
        $menu = $this->getTable("adminmenu")->fetchList(false, [], [], "AND", null, "advanced ASC, menuOrder ASC");
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

    /**
     * This action serves for adding a new admin menus
     */
    public function addAction()
    {
        $this->view->setTemplate("admin/admin-menu/add");
        $this->initForm($this->translate("ADD_ADMINMENU"), null);
        $this->addBreadcrumb(["reference"=>"/admin/adminmenu/add", "name"=>$this->translate("ADD_ADMINMENU")]);
        return $this->view;
    }

    /**
     * This action presents a modify form for AdminMenu with a given id
     * Upon POST the form is processed and saved
     */
    public function modifyAction()
    {
        $this->view->setTemplate("admin/admin-menu/modify");
        $adminMenu = $this->getTable("adminmenu")->getAdminMenu($this->getParam("id", 0))->current();
        $this->view->adminMenu = $adminMenu;
        $this->addBreadcrumb(["reference"=>"/admin/adminmenu/modify/{$adminMenu->getId()}", "name"=>$this->translate("MODIFY_ADMINMENU")." &laquo;".$adminMenu->getCaption()."&raquo;"]);
        $this->initForm($this->translate("MODIFY_ADMINMENU"), $adminMenu);
        return $this->view;
    }

    /**
     * this action deletes a admin menu with a provided id
     */
    public function deleteAction()
    {
        $this->getTable("adminmenu")->deleteAdminMenu($this->getParam("id", 0));
        $this->setLayoutMessages($this->translate("DELETE_ADMINMENU_SUCCESS"), "success");
        return $this->redirect()->toRoute('admin', ['controller' => 'adminmenu']);
    }

    public function detailAction()
    {
        $this->view->setTemplate("admin/admin-menu/detail");
        $adminmenu = $this->getTable("adminmenu")->getAdminMenu($this->getParam("id", 0), $this->language())->current();
        $this->view->adminmenu = $adminmenu;
        $this->addBreadcrumb(["reference"=>"/admin/adminmenu/detail/".$adminmenu->getId()."", "name"=>"&laquo;". $adminmenu->getCaption()."&raquo; ".$this->translate("DETAILS")]);
        return $this->view;
    }

    public function cloneAction()
    {
        $adminmenu = $this->getTable("adminmenu")->duplicate($this->getParam("id", 0));
        $this->setLayoutMessages("&laquo;".$adminmenu->getCaption()."&raquo; ".$this->translate("CLONE_SUCCESS"), "success");
        return $this->redirect()->toRoute('admin', ['controller' => 'adminmenu']);
    }

    /**
     * This is common function used by add and modify actions (to avoid code duplication)
     *
     * @param String $label
     * @param null|AdminMenu $adminMenu
     */
    public function initForm($label = '', AdminMenu $adminMenu = null)
    {
        if (!$adminMenu instanceof AdminMenu) {
            $adminMenu = new AdminMenu([], null);
        }

        /**
         * @var $form Admin\Form\AdminMenuForm
         */
        $form = $this->adminMenuForm;
        $form->get("submit")->setValue($label);
        $parents =         $this->getTable("adminmenu")->fetchList(false, [], ["parent" => 0]);

        $valueOptions = [];
        $valueOptions[0] = 'Parent menu';
        foreach ($parents as $parent) {
            $valueOptions[$parent->getId()] = $parent->getCaption();
        }

        $form->get("parent")->setValueOptions($valueOptions);
        $form->bind($adminMenu);
        $this->view->form = $form;

        if ($this->getRequest()->isPost()) {
            $form->setInputFilter($form->getInputFilter());
            $form->setData($this->getRequest()->getPost());
            if ($form->isValid()) {
                $this->getTable("adminmenu")->saveAdminMenu($adminMenu);
                $this->setLayoutMessages("&laquo;".$adminMenu->getCaption()."&raquo; ".$this->translate("SAVE_SUCCESS"), 'success');
                return $this->redirect()->toRoute('admin', ['controller' => 'adminmenu']);
            } else {
                $this->setLayoutMessages($form->getMessages(), 'error');
                return $this->redirect()->toRoute('admin', ['controller' => 'adminmenu']);
            }
        }
    }
}
