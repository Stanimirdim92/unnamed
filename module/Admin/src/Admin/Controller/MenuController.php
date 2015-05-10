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
 * @category   Admin\Menu
 * @package    ZendPress
 * @author     Stanimir Dimitrov <stanimirdim92@gmail.com>
 * @copyright  2015 Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 * @version    0.03
 * @link       TBA
 */

namespace Admin\Controller;

use Admin\Model\Menu;

class MenuController extends \Admin\Controller\IndexController
{
    /**
     * Controller name to which will redirect
     */
    const CONTROLLER_NAME = "menu";

    /**
     * Route name to which will redirect
     */
    const ADMIN_ROUTE = "admin";

    /**
     * Initialize any variables before controller actions
     *
     * @param MvcEvent $e
     */
    public function onDispatch(\Zend\Mvc\MvcEvent $e)
    {
        $this->addBreadcrumb(["reference"=>"/admin/menu", "name"=>"Menus"]);
        parent::onDispatch($e);
    }

    /**
     * This action shows the list of all (or filtered) Menu objects
     */
    public function indexAction()
        $menu = $this->getTable("Menu")->fetchList(false, ["id", "caption", "menulink", "parent",], ["language" => $this->langTranslation], "AND", null, "id, menuOrder");
        if (count($menu) > 0) {
            $submenus = $menus = [];
            foreach ($menu as $submenu) {
                if ($submenu->getParent() > 0) {
                    /**
                     * This needs to have a second empty array in order th menu to work
                     */
                    $submenus[$submenu->getParent()][] = $submenu;
                } else {
                    $menus[$submenu->getId()] = $submenu;
                }
            }
            $this->view->menus = $menus;
            $this->view->submenus = $submenus;
        }
        return $this->view;
    }

    /**
     * This action serves for adding a new object of type Menu
     */
    public function addAction()
    {
        $this->showForm('Add menu', null);
        $this->addBreadcrumb(["reference"=>"/admin/menu/add", "name"=>"Add a new menu"]);
        return $this->view;
    }

    /**
     * This action presents a modify form for Menu object with a given id
     * Upon POST the form is processed and saved
     */
    public function modifyAction()
    {
        $menu = $this->getTable("menu")->getMenu($this->getParam("id", 0), $this->langTranslation);
        $this->view->menu = $menu;
        $this->addBreadcrumb(["reference"=>"/admin/menu/modify/id/{$menu->getId()}", "name"=>"Modify menu &laquo;".$menu->toString()."&raquo;"]);
        $this->showForm('Modify menu', $menu);
        return $this->view;
    }

    /**
     * this action deletes a menu object with a provided id
     */
    public function deleteAction()
    {
        $this->getTable("menu")->deleteMenu($this->getParam("id", 0), $this->langTranslation);
        $this->cache->success = "Menu was successfully deleted";
        return $this->redirect()->toRoute(self::ADMIN_ROUTE, ['controller' => self::CONTROLLER_NAME]);
    }

    /**
     * this action shows menu details from the provided id and session language
     */
    public function detailAction()
    {
        $menu = $this->getTable("menu")->getMenu($this->getParam("id", 0), $this->langTranslation);
        $this->view->menu = $menu;
        $this->addBreadcrumb(["reference"=>"/admin/menu/detail/id/".$menu->getId()."", "name"=>"Menu &laquo;". $menu->toString()."&raquo; details"]);
        return $this->view;
    }

    /**
     * This action will clone the object with the provided id and return to the index view
     */
    public function cloneAction()
    {
        $id = (int) $this->getParam("id", 0);
        $menu = $this->getTable("menu")->duplicate($id, $this->langTranslation);
        $this->cache->success = "Menu &laquo;".$menu->toString()."&raquo; was successfully cloned";
        return $this->redirect()->toRoute(self::ADMIN_ROUTE, ['controller' => self::CONTROLLER_NAME]);
    }

    /**
     * This is common function used by add and modify actions (to avoid code duplication)
     *
     * @param string $label button title
     * @param  Menu|null $menu menu object
     */
    private function showForm($label = '', Menu $menu = null)
    {
        if ($menu == null) {
            $menu = new Menu([], null);
        }

        $menu->setServiceManager(null);
        $form = new \Admin\Form\MenuForm($menu,
                $this->getTable("Language")->fetchList(false, [], ["active" => 1], "AND", null, "name DESC"),
                $this->getTable("Menu")->fetchList(false, ['id', 'menulink', 'caption', 'language', 'parent'], ["language" => $this->langTranslation], "AND", null, "menuOrder ASC", IndexController::MAX_COUNT)
        );
        $form->get("submit")->setValue($label);
        $this->view->form = $form;
        if ($this->getRequest()->isPost()) {
            $form->setInputFilter($menu->getInputFilter());
            $form->setData(array_merge_recursive($this->getRequest()->getPost()->toArray(), $this->getRequest()->getFiles()->toArray()));
            if ($form->isValid()) {
                $formData = $form->getData();

                // see if we have menu with the exact same caption.
                if ($this->params("action") == 'add') {
                    $existingMenu = $this->getTable('menu')->fetchList(false, ['menulink', 'menutype', 'language', 'parent'], ["parent" => 0, "language" => $this->langTranslation, "menutype" => $formData['menutype'], "menulink" => $formData['menulink']], "AND", null);
                    if ($existingMenu->count() > 0) {
                        $this->cache->error = "Menu with name &laquo; ".$formData['caption']." &raquo; already exists";
                        $this->view->setTerminal(true);
                        return $this->redirect()->toRoute(self::ADMIN_ROUTE, ['controller' => self::CONTROLLER_NAME]);
                    }
                }

                $menu->exchangeArray($formData);
                if (empty($formData["parent"])) {
                    $menu->setParent(0);
                }

                $this->getTable("menu")->saveMenu($menu);
                $this->cache->success = "Menu &laquo;".$menu->toString()."&raquo; was successfully saved";
                $this->view->setTerminal(true);
                return $this->redirect()->toRoute(self::ADMIN_ROUTE, ['controller' => self::CONTROLLER_NAME]);
            } else {
                $this->formErrors($form->getMessages());
                return $this->redirect()->toRoute(self::ADMIN_ROUTE, ['controller' => self::CONTROLLER_NAME]);
            }
        }
    }
}
