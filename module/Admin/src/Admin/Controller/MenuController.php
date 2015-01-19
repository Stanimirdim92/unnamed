<?php
namespace Admin\Controller;

use Zend\Session\Container;
use Zend\File\Transfer\Adapter\Http;

use Admin\Model\Menu;
use Admin\Form\MenuForm;

use Custom\Error\AuthorizationException;


class MenuController extends \Admin\Controller\IndexController
{
    const MAX_COUNT = 200;
    const NO_ID = "no_id";

    /**
     * Initialize any variables before controller actions
     *
     * @param MvcEvent $e
     */
    public function onDispatch(\Zend\Mvc\MvcEvent $e)
    {
        $this->addBreadcrumb(array("reference"=>"/admin/menu", "name"=>"Menus"));
        parent::onDispatch($e);
    }

    /**
     * This action shows the list of all (or filtered) Menu objects
     */
    public function indexAction()
    {
        $temp = $table->fetchList(false, "parent='0' AND language='".$this->langTranslation."'", "menuOrder ASC");
        $submenus = array();
        foreach($temp as $m)
        {
            $submenus[$m->id] = $this->getTable("menu")->fetchList(false, "parent='".(int)$m->id."' AND language='{$this->langTranslation}'", "menuOrder ASC");
        }
        $this->view->menus = $temp;
        $this->view->submenus = $submenus;
        return $this->view;
    }
    
    /**
     * This action serves for adding a new object of type Menu
     */
    public function addAction()
    {
        $this->showForm('Add', null);
        $this->addBreadcrumb(array("reference"=>"/admin/menu/add", "name"=>"Add a new menu"));
        return $this->view;
    }

    /**
     * This action presents a modify form for Menu object with a given id
     * Upon POST the form is processed and saved
     */
    public function modifyAction()
    {
        $id = (int) $this->getParam("id", 0);
        if(!$id)
        {
            $this->setErrorNoParam(static::NO_ID);
            return $this->redirect()->toRoute('admin', array('controller' => 'menu'));
        }
        try
        {
            $menu = $this->getTable("menu")->getMenu($id);
        }
        catch(\Exception $ex)
        {
            $this->setErrorNoParam("Menu was not found");
            return $this->redirect()->toRoute('admin', array('controller' => 'menu'));
        }
        $this->view->menu = $menu;
        $this->addBreadcrumb(array("reference"=>"/admin/menu/modify/id/{$menu->id}", "name"=>"Modify menu &laquo;".$menu->toString()."&raquo;"));
        $this->showForm('Modify', $menu);
        return $this->view;
    }
    
    /**
     * This is common function used by add and modify actions (to avoid code duplication)
     */
    public function showForm($label='Add',Menu $menu=null)
    {
        if($menu==null) $menu = new Menu();

        $form = new MenuForm($menu,
                $this->getTable("language")->fetchList(false, null, "id ASC"),
                $this->getTable("menu")->fetchList(false, "parent='0' AND language='{$this->langTranslation}'", "menuOrder ASC", static::MAX_COUNT)
        );
        $form->get("submit")->setValue($label);
        $this->view->form = $form;
        if ($this->getRequest()->isPost())
        {
            $form->setInputFilter($menu->getInputFilter());
            $form->setData(array_merge_recursive($this->getRequest()->getPost()->toArray(),$this->getRequest()->getFiles()->toArray()));
            if ($form->isValid())
            {
                $formData = $form->getData();

                // see if we have menu with the exact same caption.
                if ($this->params("action") == 'add')
                {
                    $existingMenu = $this->getTable('menu')->fetchList(false, "parent='0' AND language='{$this->langTranslation}' AND `menutype` = '".$formData['menutype']."' AND `menulink` = '".$formData['menulink']."'");
                    if (count($existingMenu) > 0)
                    {
                        $this->cache->error = "Menu with name &laquo; ".$formData['caption']." &raquo; already exists";
                        $this->view->setTerminal(true);
                        $this->redirect()->toUrl("/admin/menu");
                        return;
                    }
                }
                $menu->exchangeArray($formData);
                // $menu->setLanguage($formData["language"]);
                 // no menus with the current session language -> set the menu to its default value
                if ($formData["parent"] == null)
                {
                    $menu->setParent(0);
                }
                $this->getTable("menu")->saveMenu($menu);
                $this->cache->success = "Menu &laquo;".$menu->toString()."&raquo; was successfully saved";
                //redirect immediately without showing a view
                $this->view->setTerminal(true);
                return $this->redirect()->toRoute('admin', array('controller' => 'menu'));
            }
        }
    }
    
    /**
     * this action deletes a menu object with a provided id
     */
    public function deleteAction()
    {
        $id = (int) $this->getParam("id", 0);
        if(! $id)
        {
            $this->setErrorNoParam($this->NO_ID);
            return $this->redirect()->toRoute('admin', array('controller' => 'menu'));
        }
        try
        {
            $menu = $this->getTable("menu")->fetchList(false, "id='{$id}' AND language='{$this->langTranslation}'");
            if (!$menu->current())
            {
                $this->cache->error = "Access denied";
                return $this->redirect()->toRoute('admin', array('controller' => 'menu'));
            }
        }
        catch(\Exception $ex)
        {
            return $this->redirect()->toRoute('admin', array('controller' => 'menu'));
        }
        $this->cache->success = "Menu &laquo;".$menu->current()->toString()."&raquo; was successfully deleted";
        $this->getTable("menu")->deleteMenu($menu->current()->id);
        return $this->redirect()->toRoute('admin', array('controller' => 'menu'));
    }


    public function detailAction()
    {
        $id = (int) $this->getParam("id", 0);
        if(! $id)
        {
            $this->setErrorNoParam($this->NO_ID);
            return $this->redirect()->toRoute('admin', array('controller' => 'menu'));
        }
        try
        {
            $menu = $this->getTable("menu")->fetchList(false, "id='{$id}' AND language='{$this->langTranslation}'");
            if (!$menu->current())
            {
                $this->cache->error = "Access denied";
                return $this->redirect()->toRoute('admin', array('controller' => 'menu'));
            }
        }
        catch(\Exception $ex)
        {
            return $this->redirect()->toRoute('admin', array('controller' => 'menu'));
        }
        $this->view->menu = $menu->current();
        $this->addBreadcrumb(array("reference"=>"/admin/menu/detail/id/".$menu->current()->id."", "name"=>"Menu &laquo;". $menu->current()->toString()."&raquo; details"));
        return $this->view;
    }
    /**
     * This action will clone the object with the provided id and return to the index view
     */
	public function cloneAction()
	{
		$id = $this->getParam("id");
        $menu = $this->getTable("menu")->duplicate($id);
        $this->cache->success = "Menu &laquo;".$menu->toString()."&raquo; was successfully cloned";
        $this->redirect()->toUrl("/admin/menu");
        return $this->view;
	}
}