<?php
namespace Admin\Controller;

use Zend\Session\Container;
use Zend\File\Transfer\Adapter\Http;

use Admin\Controller\IndexController;
use Admin\Model\Content;
use Admin\Form\ContentForm;
use Custom\Error\AuthorizationException;


class ContentController extends IndexController
{
    /**
     * Used to control the maximum number of the related objects in the forms
     *
     * @param Int $MAX_COUNT
     * @return Int
     */
    private $MAX_COUNT = 200;

    /**
     * @param string $NO_ID
     * @return string
     */
    protected $NO_ID = "no_id"; // const!!!

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
        $type = $this->getParam("type", 0);
        if ($type==1)
        {
            // fetch all contents that have value content.menu=0 and type=1
            $contentNewsWithoutMenu = $this->getTable("content")->fetchList(false,"menu='0' AND type='1' AND language='".$this->session->language."'", "id ASC");
            $this->view->contentNewsWithoutMenu = $contentNewsWithoutMenu;
        }
        if ($type==0)
        {
            // fetch all contents that have value content.menu=0 and type=0
            $contentMenusWithoutMenu = $this->getTable("content")->fetchList(false,"menu='0' AND type='0' AND language='".$this->session->language."'", "id ASC");
            $this->view->contentMenusWithoutMenu = $contentMenusWithoutMenu;
        }
        $contentsWithoutMenu =  $this->getTable("content")->fetchList(false, "menu != '0' AND language='".$this->session->language."'", "id ASC");
        $menuReport = array();
        foreach ($contentsWithoutMenu as $cwm)
        {
            if ($cwm->getMenuObject() == null)
            {
                $menuReport[] = $cwm;
            }
        }
        $this->view->menuReport = $menuReport;
        $where = "(type={$type}) AND (content.menu != '0') AND (content.language='".$this->session->language."')";
        $order = "date DESC";
        /* @var $table ContentTable */
        $table = $this->getTable("content");
        if($type==0)
            $contents = $table->fetchJoin("menu", "content.menu=menu.id", $where, "menu.parent ASC, menu.menuOrder ASC, content.date DESC");
        else
            $contents = $table->fetchList(false, $where, "content.date DESC");
        $this->view->contents = $contents;
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
        if(! $id)
        {
            $this->setErrorNoParam($this->NO_ID);
            return $this->redirect()->toRoute('admin', array('controller' => 'content'));
        }
        try
        {
            $content = $this->getTable("content")->getContent($id);
        }
        catch(\Exception $ex)
        {
            $this->setErrorNoParam("Content was not found");
            return $this->redirect()->toRoute('admin', array('controller' => 'content'));
        }
            $this->view->content = $content;
            $this->addBreadcrumb(array("reference"=>"/admin/content/modify/id/{$content->id}", "name"=>"Modify content &laquo;".$content->toString()."&raquo;"));
            $this->showForm('Modify', $content);
        return $this->view;
    }
    
    /**
     * This is common function used by add and modify actions (to avoid code duplication)
     */
    public function showForm($label='Add', $content=null)
    {
        if($content==null) $content = new Content();
        
        $orderMenus = array();
        $temp = $this->getTable("Menu")->fetchList(false, "parent='0' AND language='".$this->session->language."'", "menuOrder ASC");
        $menus = array();
        $submenus = array();
        $orderMenus = array();
        foreach($temp as $m)
        {
            $menus[] = $m;
            $submenus[$m->id] = $this->getTable("Menu")->fetchList(false, "parent='" . $m->id."' AND language='".$this->session->language."'", "menuOrder ASC");
        }
        foreach($menus as $menu)
        {
            $orderMenus[] = $menu;
            if(isset($submenus[$menu->id]) && sizeof($submenus[$menu->id])>0)
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
            // $form->setInputFilter($content->getInputFilter());
            $form->setData(array_merge_recursive($this->getRequest()->getPost()->toArray(),$this->getRequest()->getFiles()->toArray()));
            if ($form->isValid())
            {
                $formData = $form->getData();
                if(isset($formData['removepreview']) && $formData['removepreview'] && $content != null)
                {
                    if (file_exists($_SERVER['DOCUMENT_ROOT'].'/zend/public/userfiles/preview/'.$content->preview))
                    {
                        unlink($_SERVER['DOCUMENT_ROOT'] . '/zend/public/userfiles/preview/' . $content->preview);
                        $content->setPreview("");
                    }
                    else
                    {
                        $this->cache->error = "Image doesn't exist in that directory";
                        $this->view->setTerminal(true);
                        return $this->redirect()->toRoute('admin', array('controller' => 'content'));
                    }
                }
                if($formData['preview']['name']!= NULL)
                {
                    $adapter = new Http();
                    $adapter->setDestination($_SERVER['DOCUMENT_ROOT'].'/zend/public/userfiles/preview/');
                    if($adapter->isValid('preview')) 
                    {
                        // remove the old image from the directory if exists
                        if($content->preview != null && file_exists($_SERVER['DOCUMENT_ROOT'].'/zend/public/userfiles/preview/'.$content->preview))
                        {
                            unlink($_SERVER['DOCUMENT_ROOT'].'/zend/public/userfiles/preview/'.$content->preview);    
                        }
                        $param = $this->params()->fromFiles('preview');
                        $adapter->receive($param['name']);
                        $formData['preview'] = $param['name'];
                    }
                    else
                    {
                        foreach ($adapter->getMessages() as $key => $value)
                        {
                            $this->cache->error = $value;
                        }
                        $this->view->setTerminal(true);
                        return $this->redirect()->toRoute('admin', array('controller' => 'content'));
                    }       
                }
                else
                {
                    $formData['preview'] = $content->preview;
                }
                $content->exchangeArray($formData);
                // $content->setLanguage($this->session->language);

                // db table menu is empty, but we are still able to post contents.
                // if so, simply show those types of contents at the bottom of the table from the index page
                if ($formData["menu"] == null)
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
                return $this->redirect()->toRoute('admin', array('controller' => 'content'));
            }
            else
            {
                $error = '';
                foreach($form->getMessages() as $msg)
                {
                    foreach ($msg as $key => $value)
                    {
                        $error = $value;
                    }
                }
                $this->setErrorNoParam($error);
                return $this->redirect()->toRoute('admin', array('controller' => 'content'));
            }
        }
    }
    
    /**
     * this action deletes a content object with a provided id
     */
    public function deleteAction()
    {
        $id = (int) $this->getParam("id", 0);
        if(! $id)
        {
            $this->setErrorNoParam($this->NO_ID);
            return $this->redirect()->toRoute('admin', array('controller' => 'content'));
        }
        try
        {
            $content = $this->getTable("content")->fetchList(false, "id='{$id}' AND language='".$this->session->language."'");
            if (!$content->current())
            {
                throw new AuthorizationException($this->session->ERROR_AUTHORIZATION);
            }
            $this->getTable("content")->deleteContent($content->current()->id);
        }
        catch(\Exception $ex)
        {
            return $this->redirect()->toRoute('admin', array('controller' => 'content'));
        }
        $this->cache->success = "Content &laquo;".$content->current()->toString()."&raquo; was successfully deleted";
        return $this->redirect()->toRoute('admin', array('controller' => 'content'));
    }


    public function detailAction()
    {
        $id = (int) $this->getParam("id", 0);
        if(! $id)
        {
            $this->setErrorNoParam($this->NO_ID);
            return $this->redirect()->toRoute('admin', array('controller' => 'content'));
        }
        try
        {
            $content = $this->getTable("content")->fetchList(false, "id='{$id}' AND language='".$this->session->language."'");
            if (!$content->current())
            {
                throw new AuthorizationException($this->session->ERROR_AUTHORIZATION);
            }
            $this->view->content = $content->current();
        }
        catch(\Exception $ex)
        {
            return $this->redirect()->toRoute('admin', array('controller' => 'content'));
        }
        $this->addBreadcrumb(array("reference"=>"/admin/content/detail/id/".$content->current()->id."", "name"=>"Content &laquo;". $content->current()->toString()."&raquo; details"));

        return $this->view;
    }

    /**
     * This action will clone the object with the provided id and return to the index view
     */
    public function cloneAction()
    {
        $id = $this->getParam("id");
        $content = $this->getTable("content")->duplicate($id);
        $this->cache->success = "Content &laquo;".$content->toString()."&raquo; was successfully cloned";
        $this->redirect()->toUrl("/admin/content");
        return $this->view;
    }
}
