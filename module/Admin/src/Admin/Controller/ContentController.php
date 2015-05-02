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
 * CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT,
 * TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE
 * SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 *
 * @category   Admin\Content
 * @package    ZendPress
 * @author     Stanimir Dimitrov <stanimirdim92@gmail.com>
 * @copyright  2015 Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 * @version    0.03
 * @link       TBA
 */

namespace Admin\Controller;

use Admin\Model\Content;

class ContentController extends \Admin\Controller\IndexController
{
    /**
     * Controller name to which will redirect
     */
    const CONTROLLER_NAME = "content";

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
        $this->addBreadcrumb(array("reference"=>"/admin/content", "name"=>"Contents"));
        parent::onDispatch($e);
    }

    /**
     * This action shows the list of all (or filtered) Content objects
     */
    public function indexAction()
    {
        $type = (int) $this->getParam("type", 0);
        if ($type === 1) {
            // fetch all contents that have value content.menu=0 and type=1
            $this->view->contentNewsWithoutMenu = $this->getTable("content")->fetchList(false, array("menu", "type", "language"), "menu='0' AND type='1' AND language='".$this->langTranslation."'", null, null, "id ASC");
            $this->view->contents = $this->getTable("content")->fetchList(false, array(), "(type='1' AND content.menu != '0') AND (content.language='".$this->langTranslation."')", null, null,  "content.date DESC");
        }
        if ($type === 0) {
            // fetch all contents that have value content.menu=0 and type=0
            $this->view->contentMenusWithoutMenu = $this->getTable("content")->fetchList(false, array("menu", "type", "language"), "menu='0' AND type='0' AND language='".$this->langTranslation."'", null, null, "id ASC");
            $this->view->contents = $this->getTable("content")->fetchJoin(false, "menu", "content.menu=menu.id", "(type='0' AND content.menu != '0') AND (content.language='".$this->langTranslation."')", null, null, "menu.parent ASC, menu.menuOrder ASC, content.date DESC");
        }

        $contentsWithoutMenu =  $this->getTable("content")->fetchList(false, array("menu", "language"), "menu != '0' AND language='".$this->langTranslation."'", null, null, "id ASC");
        if (count($contentsWithoutMenu) > 0) {
            $menuReport = array();
            foreach ($contentsWithoutMenu as $cwm) {
                if (!$cwm->getMenuObject()) {
                    $menuReport[] = $cwm;
                }
            }
            $this->view->menuReport = $menuReport;
        }
        return $this->view;
    }

    /**
     * This action serves for adding a new object of type Content
     */
    public function addAction()
    {
        $this->showForm('Add', null);
        $this->addBreadcrumb(array("reference"=>"/admin/content/add", "name"=>"Add a new content"));
        return $this->view;
    }

    /**
     * This action presents a modify form for Content object with a given id and session language
     * Upon POST the form is processed and saved
     */
    public function modifyAction()
    {
        $content = $this->getTable("content")->getContent($this->getParam("id", 0), $this->langTranslation);
        $this->view->content = $content;
        $this->addBreadcrumb(array("reference"=>"/admin/content/modify/id/{$content->id}", "name"=>"Modify content &laquo;".$content->toString()."&raquo;"));
        $this->showForm('Modify', $content);
        return $this->view;
    }

    /**
     * this action deletes a content object with a provided id and session language
     */
    public function deleteAction()
    {
        $content = $this->getTable("content")->deleteContent($this->getParam("id", 0), $this->langTranslation);
        $this->cache->success = "Content was successfully deleted";
        return $this->redirect()->toRoute(self::ADMIN_ROUTE, array('controller' => self::CONTROLLER_NAME));
    }

    /**
     * this action shows content details from the provided id and session language
     */
    public function detailAction()
    {
        $content = $this->getTable("content")->getContent($this->getParam("id", 0), $this->langTranslation);
        $this->view->content = $content;
        $this->addBreadcrumb(array("reference"=>"/admin/content/detail/id/".$content->getId()."", "name"=>"Content &laquo;". $content->toString()."&raquo; details"));
        return $this->view;
    }

    /**
     * This action will clone the object with the provided id and return to the index view
     */
    public function cloneAction()
    {
        $content = $this->getTable("content")->duplicate($this->getParam("id", 0), $this->langTranslation);
        $this->cache->success = "Content &laquo;".$content->toString()."&raquo; was successfully cloned";
        $this->redirect()->toUrl("/admin/content");
        return $this->view;
    }

    /**
     * This is common function used by add and modify actions (to avoid code duplication)
     *
     * @param string $label button title
     * @param  Content|null $menu menu object
     */
    private function showForm($label = 'Add', Content $content = null)
    {
        if ($content==null) {
            $content = new Content(array(), null);
        }

        $orderMenus = $menus = $submenus = array();
        $menus = $this->getTable("Menu")->fetchList(false, array("parent", "language", "id", "caption"), array("parent" => 0, "language" => $this->langTranslation), "AND", null, "menuOrder ASC");
        foreach ($menus as $m) {
            $m->setServiceManager(null);
            $submenus[$m->id] = $this->getTable("Menu")->fetchList(false, array("parent", "language", "id", "caption"), array("parent" => $m->getId(), "language" => $this->langTranslation), "AND", null, "menuOrder ASC");
        }
        foreach ($menus as $menu) {
            $menu->setServiceManager(null);
            $orderMenus[] = $menu;
            if (isset($submenus[$menu->id]) && count($submenus[$menu->id]) > 0) {
                foreach ($submenus[$menu->id] as $sub) {
                    $orderMenus[] = $sub;
                }
            }
        }
        $form = new \Admin\Form\ContentForm($content, $orderMenus, $this->getTable("Language")->fetchList(false, array(), array("active" => 1), "AND", null, "id ASC"));
        $form->get("submit")->setValue($label);
        $this->view->form = $form;
        if ($this->getRequest()->isPost()) {
            $form->setInputFilter($content->getInputFilter());
            $form->setData(array_merge_recursive($this->getRequest()->getPost()->toArray(),$this->getRequest()->getFiles()->toArray()));
            if ($form->isValid()) {
                $formData = $form->getData();
                if (isset($formData['removepreview']) && $formData['removepreview'] && $content != null) {
                    if (!is_file($_SERVER['DOCUMENT_ROOT'].'/userfiles/preview/'.$content->getPreview())) {
                        $this->cache->error = "Image doesn't exist in that directory";
                        $this->view->setTerminal(true);
                        return $this->redirect()->toRoute(self::ADMIN_ROUTE, array('controller' => self::CONTROLLER_NAME));
                    }
                    unlink($_SERVER['DOCUMENT_ROOT'] . '/userfiles/preview/' . $content->getPreview());
                    $content->setPreview("");
                }
                if ($formData['preview']['name'] != null) {
                    $adapter = new \Zend\File\Transfer\Adapter\Http();
                    $adapter->setDestination($_SERVER['DOCUMENT_ROOT'].'/userfiles/preview/');
                    if ($adapter->isValid('preview')) {
                        // remove the old image from the directory if exists
                        if ($content->preview != null && is_file($_SERVER['DOCUMENT_ROOT'].'/userfiles/preview/'.$content->getPreview())) {
                            unlink($_SERVER['DOCUMENT_ROOT'].'/userfiles/preview/'.$content->getPreview());
                        }
                        $param = $this->params()->fromFiles('preview');
                        $adapter->receive($param['name']);
                        $formData['preview'] = $param['name'];
                    } else {
                        $error = array();
                        foreach ($adapter->getMessages() as $key => $value) {
                            $error[] = $value;
                        }
                        $this->setErrorNoParam($error);
                        return $this->redirect()->toRoute(self::ADMIN_ROUTE, array('controller' => self::CONTROLLER_NAME));
                    }
                } else {
                    $formData['preview'] = $content->getPreview();
                }
                $content->exchangeArray($formData);

                // db table menu is empty, but we are still able to post contents.
                // if so, simply show those types of contents at the bottom of the table from the index page
                if (!$formData["menu"]) {
                    $content->setMenu(0);
                } else {
                    $content->setMenu($formData['menu']);
                }
                $this->getTable("content")->saveContent($content);
                $this->cache->success = "Content &laquo;".$content->toString()."&raquo; was successfully saved";
                $this->view->setTerminal(true);
                return $this->redirect()->toRoute(self::ADMIN_ROUTE, array('controller' => self::CONTROLLER_NAME));
            } else {
                $error = array();
                foreach ($form->getMessages() as $msg) {
                    foreach ($msg as $key => $value) {
                        $error[] = $value;
                    }
                }
                $this->setErrorNoParam($error);
                return $this->redirect()->toRoute(self::ADMIN_ROUTE, array('controller' => self::CONTROLLER_NAME));
            }
        }
    }
}
