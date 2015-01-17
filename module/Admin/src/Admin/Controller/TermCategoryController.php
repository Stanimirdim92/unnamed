<?php
namespace Admin\Controller;

use Zend\Session\Container;

use Admin\Controller\IndexController;
use Admin\Model\TermCategory;
use Admin\Form\TermCategoryForm;
use Admin\Form\TermCategorySearchForm;

use Custom\Plugins\Functions;

class TermCategoryController extends IndexController
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
        $this->addBreadcrumb(array("reference"=>"/admin/termcategory", "name"=>"Term categories"));
        parent::onDispatch($e);
    }

    /**
    * This action shows the list of all (or filtered) TermCategory objects
    */
    public function indexAction()
    {
        $order = "name ASC";
        $paginator = $this->getTable("termcategory")->fetchList(true, null, $order);
        $paginator->setCurrentPageNumber((int)$this->params("page",1));
        $paginator->setItemCountPerPage(50);
        $this->view->paginator = $paginator;
        return $this->view;
    }

    /**
    * This action serves for adding a new object of type TermCategory
    */
    public function addAction()
    {
        $this->showForm("Add", null);
        $this->addBreadcrumb(array("reference"=>"/admin/termcategory/add", "name"=>"Add new term category"));
        return $this->view;
    }

    /**
    * This action presents a modify form for TermCategory object with a given id
    * Upon POST the form is processed and saved
    */
    public function modifyAction()
    {
        $id = (int) $this->getParam('id', 0);
        if(!$id)
        {
            $this->setErrorNoParam($this->NO_ID);
            return $this->redirect()->toRoute('admin', array('controller' => 'termcategory'));
        }
        try
        {
            $termcategory = $this->getTable("termcategory")->getTermCategory($id);
            $this->view->termcategory = $termcategory;
            $this->addBreadcrumb(array("reference"=>"/admin/termcategory/modify/id/{$termcategory->id}", "name"=>"Modify term ID &laquo;{$termcategory->id}&raquo;"));
            $this->showForm("Modify", $termcategory);
        }
        catch(\Exception $ex)
        {
            $this->setErrorNoParam("Term category not found");
            return $this->redirect()->toRoute('admin', array('controller' => 'termcategory'));
        }
        return $this->view;
    }

    /**
     * This is common function used by add and modify actions (to avoid code duplication)
     *
     * @param String $label
     * @param null|TermCategory $termcategory
     */
    public function showForm($label = '', $termcategory = null)
    {
        if($termcategory == null)
        {
            $termcategory = new TermCategory();
        }

        $form = new TermCategoryForm($termcategory);
        $form->get("submit")->setValue($label);

        $this->view->form = $form;
        if ($this->getRequest()->isPost())
        {
            $form->setInputFilter($termcategory->getInputFilter());
            $form->setData($this->getRequest()->getPost());
            if ($form->isValid())
            {
                $termcategory->exchangeArray($form->getData());
                $this->getTable("termcategory")->saveTermCategory($termcategory);
                $this->cache->success = "Term category &laquo;".$termcategory->toString()."&raquo; was successfully saved";
                $this->view->setTerminal(true);
                return $this->redirect()->toRoute('admin', array('controller' => 'termcategory'));
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
                return $this->redirect()->toRoute('admin', array('controller' => 'termcategory'));
            }
        }
    }

    /**
     * this action deletes a TermCategory object with a provided id
     */
    public function deleteAction()
    {
        $id = (int) $this->getParam('id', 0);
        if(!$id)
        {
            $this->setErrorNoParam($this->NO_ID);
            return $this->redirect()->toRoute('admin', array('controller' => 'termcategory'));
        }
        try
        {
            $this->getTable("termcategory")->deleteTermCategory($id);
        }
        catch(\Exception $ex)
        {
            $this->setErrorNoParam("Term category not found");
            return $this->redirect()->toRoute('admin', array('controller' => 'termcategory'));
        }
        $this->cache->success = "Term category was successfully deleted";
        return $this->redirect()->toRoute('admin', array('controller' => 'termcategory'));
    }

    public function cloneAction()
    {
        $id = $this->getParam("id", 0);
        $termcategory = $this->getTable("termcategory")->duplicate($id);
        $this->cache->success = "Term category &laquo;".$termcategory->toString()."&raquo; was successfully cloned";
        $this->redirect()->toUrl("/admin/termcategory");
        return $this->view;
    }
}
