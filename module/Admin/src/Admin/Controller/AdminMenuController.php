<?php
namespace Admin\Controller;

use Zend\Session\Container;

use Admin\Controller\IndexController;
use Admin\Model\AdminMenu;
use Admin\Form\AdminMenuForm;
use Admin\Form\AdminMenuSearchForm;

class AdminMenuController extends IndexController
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
        $this->addBreadcrumb(array("reference"=>"/admin/adminmenu", "name"=>"Admin Menus"));
        parent::onDispatch($e);
    }

    /**
     * This action shows the list of all (or filtered) AdminMenu objects
     */
    public function indexAction()
    {
        $search = $this->getParam("search", null);
        $where = "parent = '0'";
        if($search != null)
        {
            $where = "(parent = '0') AND (`caption` LIKE '%{$search}%' OR `menuOrder` LIKE '%{$search}%' OR `controller` LIKE '%{$search}%'  OR `action` LIKE '%{$search}%'  OR `class` LIKE '%{$search}%')";
        }

        $order = "advanced ASC, menuOrder ASC";
        $menus = $this->getTable("adminmenu")->fetchList(false, $where, $order);
        $this->view->menus = $menus;
        $submenus = array();
        foreach($menus as $m)
        {
            $submenus[$m->id] = $this->getTable("adminmenu")->fetchList(false,"parent='{$m->id}'", $order);
        }
        $this->view->submenus = $submenus;
        $form = new AdminMenuSearchForm();
        $form->get("submit")->setValue($this->session->SEARCH);
        $form->get("search")->setValue($search);
        $this->view->form = $form;
        return $this->view;
    }

    /**
     * This action serves for adding a new object of type AdminMenu
     */
    public function addAction()
    {
        $this->showForm('Add', null);
        $this->addBreadcrumb(array("reference"=>"/admin/adminmenu/add", "name"=>"Add new admin menu"));
        return $this->view;
    }

    /**
     * This action presents a modify form for AdminMenu object with a given id
     * Upon POST the form is processed and saved
     */
    public function modifyAction()
    {
        $id = (int) $this->getParam("id", 0);
        if(!$id)
        {
            $this->setErrorNoParam($this->NO_ID);
            return $this->redirect()->toRoute('admin', array('controller' => 'adminmenu'));
        }
        try
        {
            $adminMenu = $this->getTable("adminmenu")->getAdminMenu($id);
            $this->view->adminMenu = $adminMenu;
            $this->addBreadcrumb(array("reference"=>"/admin/adminmenu/modify/id/{$adminMenu->id}", "name"=>"Modify admin menu &laquo;".$adminMenu->toString()."&raquo;"));
            $this->showForm("Modify", $adminMenu);
        }
        catch(\Exception $ex)
        {
            $this->setErrorNoParam("Admin menu not found");
            return $this->redirect()->toRoute('admin', array('controller' => 'adminmenu'));
        }
        return $this->view;
    }
    
    /**
     * This is common function used by add and modify actions (to avoid code duplication)
     *
     * @param String $label
     * @param null|AdminMenu $user
     */
    public function showForm($label='Add', $adminMenu=null)
    {
        if($adminMenu==null) $adminMenu = new AdminMenu();
        $form = new AdminMenuForm($adminMenu,
                $this->getTable("adminmenu")->fetchList(false, "parent='0'", "caption ASC")
        );
        $form->get("submit")->setValue($label);
        $this->view->form = $form;
        if ($this->getRequest()->isPost()) 
        {
            $form->setInputFilter($adminMenu->getInputFilter());
            $form->setData($this->getRequest()->getPost());
            if ($form->isValid())
            {
                $adminMenu->exchangeArray($form->getData());
                $this->getTable("adminmenu")->saveAdminMenu($adminMenu);
                $this->cache->success = "Admin menu &laquo;".$adminMenu->toString()."&raquo; was successfully saved";
                $this->view->setTerminal(true);
                return $this->redirect()->toRoute('admin', array('controller' => 'adminmenu'));
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
                return $this->redirect()->toRoute('admin', array('controller' => 'adminmenu'));
            }
        }
    }
    
    /**
     * this action deletes a adminmenu object with a provided id
     */
    public function deleteAction()
    {
        $id = (int) $this->getParam('id', 0);
        if(!$id)
        {
            $this->setErrorNoParam($this->NO_ID);
            return $this->redirect()->toRoute('admin', array('controller' => 'adminmenu'));
        }
        try
        {
            $this->getTable("adminmenu")->deleteAdminMenu($id);
        }
        catch(\Exception $ex)
        {
            $this->setErrorNoParam("Admin menu not found");
            return $this->redirect()->toRoute('admin', array('controller' => 'adminmenu'));
        }
        $this->cache->success = "Admin menu was successfully deleted";
        return $this->redirect()->toRoute('admin', array('controller' => 'adminmenu'));
    }

    public function detailAction()
    {
        $id = (int) $this->getParam('id', 0);
        if(!$id)
        {
            $this->setErrorNoParam($this->NO_ID);
            return $this->redirect()->toRoute('admin', array('controller' => 'adminmenu'));
        }
        try
        {
            $adminmenu = $this->getTable("adminmenu")->getAdminMenu($id);
        }
        catch(\Exception $ex)
        {
            $this->setErrorNoParam("Admin menu not found");
            return $this->redirect()->toRoute('admin', array('controller' => 'adminmenu'));
        }
        $this->view->adminmenu = $adminmenu;
        $this->addBreadcrumb(array("reference"=>"/admin/adminmenu/detail/id/{$adminmenu->id}", "name"=> "Admin menu &laquo;". $adminmenu->toString()."&raquo; details"));

        return $this->view;
    }

    public function cloneAction()
    {
        $id = $this->getParam("id", 0);
        $adminmenu = $this->getTable("adminmenu")->duplicate($id);
        $this->cache->success = "Admin menu &laquo;".$adminmenu->toString()."&raquo; was successfully cloned";
        $this->redirect()->toUrl("/admin/adminmenu");
        return $this->view;
    }
}
