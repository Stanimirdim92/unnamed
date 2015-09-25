<?php
/**
 * MIT License.
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
 * @version    0.0.13
 * @link       TBA
 */

namespace Admin\Controller;

use Admin\Model\AdminMenu;
use Admin\Form\AdminMenuForm;

final class AdminMenuController extends IndexController
{
    /**
     * @var AdminMenuForm $adminMenuForm
     */
    private $adminMenuForm = null;

    /**
     * @param AdminMenuForm $adminMenuForm
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
     *
     * @return ViewModel
     */
    public function indexAction()
    {
        $this->getView()->setTemplate("admin/admin-menu/index");
        $menu = $this->getTable("adminmenu")->fetchList(false, [], [], "AND", null, "advanced ASC, menuOrder ASC");
        if (count($menu) > 0) {
            $menus = ["menus" => null, "submenus" => null];
            foreach ($menu as $submenu) {
                if ($submenu->getParent() > 0) {
                    $menus["submenus"][$submenu->getParent()][] = $submenu;
                } else {
                    $menus["menus"][$submenu->getId()] = $submenu;
                }
            }
            $this->getView()->menus = $menus["menus"];
            $this->getView()->submenus = $menus["submenus"];
        }
        return $this->getView();
    }

    /**
     * This action serves for adding a new admin menus
     *
     * @return ViewModel
     */
    protected function addAction()
    {
        $this->getView()->setTemplate("admin/admin-menu/add");
        $this->initForm($this->translate("ADD_ADMINMENU"), null);
        $this->addBreadcrumb(["reference"=>"/admin/adminmenu/add", "name"=>$this->translate("ADD_ADMINMENU")]);
        return $this->getView();
    }

    /**
     * This action presents a modify form for AdminMenu with a given id
     * Upon POST the form is processed and saved
     *
     * @return ViewModel
     */
    protected function modifyAction()
    {
        $this->getView()->setTemplate("admin/admin-menu/modify");
        $adminMenu = $this->getTable("adminmenu")->getAdminMenu((int) $this->getParam("id", 0))->current();
        $this->getView()->adminMenu = $adminMenu;
        $this->addBreadcrumb(["reference"=>"/admin/adminmenu/modify/{$adminMenu->getId()}", "name"=>$this->translate("MODIFY_ADMINMENU")." &laquo;".$adminMenu->getCaption()."&raquo;"]);
        $this->initForm($this->translate("MODIFY_ADMINMENU"), $adminMenu);
        return $this->getView();
    }

    /**
     * this action deletes a admin menu with a provided id
     */
    protected function deleteAction()
    {
        $this->getTable("adminmenu")->deleteAdminMenu((int)$this->getParam("id", 0));
        $this->setLayoutMessages($this->translate("DELETE_ADMINMENU_SUCCESS"), "success");
        return $this->redirect()->toRoute('admin/default', ['controller' => 'adminmenu']);
    }

    /**
     * @return ViewModel
     */
    protected function detailAction()
    {
        $this->getView()->setTemplate("admin/admin-menu/detail");
        $adminmenu = $this->getTable("adminmenu")->getAdminMenu((int)$this->getParam("id", 0), $this->language())->current();
        $this->getView()->adminmenu = $adminmenu;
        $this->addBreadcrumb(["reference"=>"/admin/adminmenu/detail/".$adminmenu->getId()."", "name"=>"&laquo;". $adminmenu->getCaption()."&raquo; ".$this->translate("DETAILS")]);
        return $this->getView();
    }

    protected function cloneAction()
    {
        $adminmenu = $this->getTable("adminmenu")->duplicate((int)$this->getParam("id", 0));
        $this->setLayoutMessages("&laquo;".$adminmenu->getCaption()."&raquo; ".$this->translate("CLONE_SUCCESS"), "success");
        return $this->redirect()->toRoute('admin/default', ['controller' => 'adminmenu']);
    }

    /**
     * This is common function used by add and modify actions (to avoid code duplication)
     *
     * @param String $label
     * @param AdminMenu $adminMenu
     */
    private function initForm($label = '', AdminMenu $adminMenu = null)
    {
        if (!$adminMenu instanceof AdminMenu) {
            $adminMenu = new AdminMenu([]);
        }

        /**
         * @var $form Admin\Form\AdminMenuForm
         */
        $form = $this->adminMenuForm;
        $form->get("submit")->setValue($label);
        $form->bind($adminMenu);
        $this->getView()->form = $form;

        if ($this->getRequest()->isPost()) {
            $form->setInputFilter($form->getInputFilter());
            $form->setData($this->getRequest()->getPost());
            if ($form->isValid()) {
                $this->getTable("adminmenu")->saveAdminMenu($adminMenu);
                $this->setLayoutMessages("&laquo;".$adminMenu->getCaption()."&raquo; ".$this->translate("SAVE_SUCCESS"), 'success');
            } else {
                $this->setLayoutMessages($form->getMessages(), 'error');
            }
            return $this->redirect()->toRoute('admin/default', ['controller' => 'adminmenu']);
        }
    }
}
