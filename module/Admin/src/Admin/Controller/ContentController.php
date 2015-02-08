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

use Zend\Session\Container;
use Zend\File\Transfer\Adapter\Http;

use Admin\Model\Content;
use Admin\Form\ContentForm;

use Custom\Error\AuthorizationException;


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
        if ($type === 1)
        {
            // fetch all contents that have value content.menu=0 and type=1
            $this->view->contentNewsWithoutMenu = $this->getTable("content")->fetchList(false, array("menu", "type", "language"), "menu='0' AND type='1' AND language='".$this->langTranslation."'", "id ASC");
        }
        if ($type === 0)
        {
            // fetch all contents that have value content.menu=0 and type=0
            $this->view->contentMenusWithoutMenu = $this->getTable("content")->fetchList(false, array("menu", "type", "language"), "menu='0' AND type='0' AND language='".$this->langTranslation."'", "id ASC");
        }

        $contentsWithoutMenu =  $this->getTable("content")->fetchList(false, array("menu", "language"), "menu != '0' AND language='".$this->langTranslation."'", "id ASC");
        $menuReport = array();
        foreach ($contentsWithoutMenu as $cwm)
        {
            if (!$cwm->getMenuObject())
            {
                $menuReport[] = $cwm;
            }
        }
        $this->view->menuReport = $menuReport;

        if($type === 0)
        {
            $this->view->contents = $this->getTable("content")->fetchJoin(false, "menu", "content.menu=menu.id", "(type='{$type}') AND (content.menu != '0') AND (content.language='".$this->langTranslation."')", "menu.parent ASC, menu.menuOrder ASC, content.date DESC");
        }
        else
        {
            $this->view->contents = $this->getTable("content")->fetchList(false, array(), "(type='{$type}') AND (content.menu != '0') AND (content.language='".$this->langTranslation."')", "content.date DESC");
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
     * This action presents a modify form for Content object with a given id
     * Upon POST the form is processed and saved
     */
    public function modifyAction()
    {
        $id = (int) $this->getParam("id", 0);
        if(!$id)
        {
            $this->setErrorNoParam(IndexController::NO_ID);
            return $this->redirect()->toRoute(self::ADMIN_ROUTE, array('controller' => self::CONTROLLER_NAME));
        }
        try
        {
            $content = $this->getTable("content")->getContent($id, $this->langTranslation);
        }
        catch(\Exception $ex)
        {
            $this->setErrorNoParam("Content was not found");
            return $this->redirect()->toRoute(self::ADMIN_ROUTE, array('controller' => self::CONTROLLER_NAME));
        }
        $this->view->content = $content;
        $this->addBreadcrumb(array("reference"=>"/admin/content/modify/id/{$content->id}", "name"=>"Modify content &laquo;".$content->toString()."&raquo;"));
        $this->showForm('Modify', $content);
        return $this->view;
    }
    
    /**
     * This is common function used by add and modify actions (to avoid code duplication)
     *
     * @param string $label button title
     * @param  Content|null $menu menu object
     */
    public function showForm($label = 'Add', Content $content = null)
    {
        if($content==null) $content = new Content(array(), null);
        
        $orderMenus = $menus = $submenus = array();
        $temp = $this->getTable("Menu")->fetchList(false, array("parent", "language", "id", "caption"), "parent='0' AND language='".$this->langTranslation."'", "menuOrder ASC");
        foreach($temp as $m)
        {
            $menus[] = $m;
            $submenus[$m->id] = $this->getTable("Menu")->fetchList(false, array("parent", "language", "id", "caption"), "parent='" . $m->id."' AND language='".$this->langTranslation."'", "menuOrder ASC");
        }
        foreach($menus as $menu)
        {
            $orderMenus[] = $menu;
            if(isset($submenus[$menu->id]) && count($submenus[$menu->id])>0)
            {
                foreach($submenus[$menu->id] as $sub)
                {
                    $orderMenus[] = $sub;
                }
            }
        }
        $form = new ContentForm($content,
                                $orderMenus,
                                $this->getTable("language")->fetchList(false, null, "id ASC")
        );
        $form->get("submit")->setValue($label);
        $this->view->form = $form;
        if ($this->getRequest()->isPost())
        {
            $form->setInputFilter($content->getInputFilter());
            $form->setData(array_merge_recursive($this->getRequest()->getPost()->toArray(),$this->getRequest()->getFiles()->toArray()));
            if ($form->isValid())
            {
                $formData = $form->getData();
                if(isset($formData['removepreview']) && $formData['removepreview'] && $content != null)
                {
                    if (file_exists($_SERVER['DOCUMENT_ROOT'].'/public/userfiles/preview/'.$content->preview))
                    {
                        unlink($_SERVER['DOCUMENT_ROOT'] . '/public/userfiles/preview/' . $content->preview);
                        $content->setPreview("");
                    }
                    else
                    {
                        $this->cache->error = "Image doesn't exist in that directory";
                        $this->view->setTerminal(true);
                        return $this->redirect()->toRoute(self::ADMIN_ROUTE, array('controller' => self::CONTROLLER_NAME));
                    }
                }
                if($formData['preview']['name']!= NULL)
                {
                    $adapter = new Http();
                    $adapter->setDestination($_SERVER['DOCUMENT_ROOT'].'/public/userfiles/preview/');
                    if($adapter->isValid('preview')) 
                    {
                        // remove the old image from the directory if exists
                        if($content->preview != null && file_exists($_SERVER['DOCUMENT_ROOT'].'/public/userfiles/preview/'.$content->preview))
                        {
                            unlink($_SERVER['DOCUMENT_ROOT'].'/public/userfiles/preview/'.$content->preview);    
                        }
                        $param = $this->params()->fromFiles('preview');
                        $adapter->receive($param['name']);
                        $formData['preview'] = $param['name'];
                    }
                    else
                    {
                        $error = array();
                        foreach ($adapter->getMessages() as $key => $value)
                        {
                            $error[] = $value;
                        }
                        $this->setErrorNoParam($error);
                        return $this->redirect()->toRoute(self::ADMIN_ROUTE, array('controller' => self::CONTROLLER_NAME));
                    }       
                }
                else
                {
                    $formData['preview'] = $content->preview;
                }
                $content->exchangeArray($formData);
                
                // db table menu is empty, but we are still able to post contents.
                // if so, simply show those types of contents at the bottom of the table from the index page
                if (!$formData["menu"])
                {
                    $content->setMenu(0);
                }
                else
                {
                    $content->setMenu($formData['menu']);
                }
                $this->getTable("content")->saveContent($content);
                $this->cache->success = "Content &laquo;".$content->toString()."&raquo; was successfully saved";
                $this->view->setTerminal(true);
                return $this->redirect()->toRoute(self::ADMIN_ROUTE, array('controller' => self::CONTROLLER_NAME));
            }
            else
            {
                $error = array();
                foreach($form->getMessages() as $msg)
                {
                    foreach ($msg as $key => $value)
                    {
                        $error[] = $value;
                    }
                }
                $this->setErrorNoParam($error);
                return $this->redirect()->toRoute(self::ADMIN_ROUTE, array('controller' => self::CONTROLLER_NAME));
            }
        }
    }
    
    /**
     * this action deletes a content object with a provided id
     */
    public function deleteAction()
    {
        $id = (int) $this->getParam("id", 0);
        if(!$id)
        {
            $this->setErrorNoParam(IndexController::NO_ID);
            return $this->redirect()->toRoute(self::ADMIN_ROUTE, array('controller' => self::CONTROLLER_NAME));
        }
        try
        {
            $content = $this->getTable("content")->getContent($id, $this->langTranslation);
            if (!$content)
            {
                throw new AuthorizationException(IndexController::ACCESS_DENIED);
                return $this->redirect()->toRoute(self::ADMIN_ROUTE, array('controller' => self::CONTROLLER_NAME));
            }
        }
        catch(\Exception $ex)
        {
            $this->setErrorNoParam($ex->getMessage());
            return $this->redirect()->toRoute(self::ADMIN_ROUTE, array('controller' => self::CONTROLLER_NAME));
        }
        $this->getTable("content")->deleteContent($content->id);
        $this->cache->success = "Content &laquo;".$content->toString()."&raquo; was successfully deleted";
        return $this->redirect()->toRoute(self::ADMIN_ROUTE, array('controller' => self::CONTROLLER_NAME));
    }


    public function detailAction()
    {
        $id = (int) $this->getParam("id", 0);
        if(!$id)
        {
            $this->setErrorNoParam(IndexController::NO_ID);
            return $this->redirect()->toRoute(self::ADMIN_ROUTE, array('controller' => self::CONTROLLER_NAME));
        }
        try
        {
            $content = $this->getTable("content")->getContent($id, $this->langTranslation);
            if (!$content)
            {
                throw new AuthorizationException(IndexController::ACCESS_DENIED);
                return $this->redirect()->toRoute(self::ADMIN_ROUTE, array('controller' => self::CONTROLLER_NAME));
            }
        }
        catch(\Exception $ex)
        {
            $this->setErrorNoParam($ex->getMessage());
            return $this->redirect()->toRoute(self::ADMIN_ROUTE, array('controller' => self::CONTROLLER_NAME));
        }
        $this->view->content = $content;
        $this->addBreadcrumb(array("reference"=>"/admin/content/detail/id/".$content->getId()."", "name"=>"Content &laquo;". $content->toString()."&raquo; details"));
        return $this->view;
    }

    /**
     * This action will clone the object with the provided id and return to the index view
     */
    public function cloneAction()
    {
        $id = (int) $this->getParam("id", 0);
        $content = $this->getTable("content")->duplicate($id, $this->langTranslation);
        $this->cache->success = "Content &laquo;".$content->toString()."&raquo; was successfully cloned";
        $this->redirect()->toUrl("/admin/content");
        return $this->view;
    }
}
