<?php
namespace Admin\Controller;

use Zend\Session\Container;

use Admin\Controller\IndexController;
use Admin\Model\Term;
use Admin\Form\TermForm;
use Admin\Form\TermSearchForm;

use Custom\Plugins\Functions;

class TermController extends IndexController
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
        $this->addBreadcrumb(array("reference"=>"/admin/term", "name"=>"Terms"));
        parent::onDispatch($e);
    }

    /**
    * This action shows the list of all (or filtered) Term objects
    */
    public function indexAction()
    {
        $search = $this->getparam('search', null);
        $where = null;
        if($search != null)
        {
            $where = "`name` LIKE '%{$search}%'";
        }
        $order = "name ASC";
        $paginator = $this->getTable("term")->fetchList(true, $where, $order);
        $paginator->setCurrentPageNumber((int)$this->params("page",1));
        $paginator->setItemCountPerPage(50);
        $this->view->paginator = $paginator;
        $form = new TermSearchForm();
        $form->get("submit")->setValue("Search");
        $form->get("search")->setValue($search);
        $this->view->form = $form;
        return $this->view;
    }

    /**
    * This action serves for adding a new object of type Term
    */
    public function addAction()
    {
        $this->showForm("Add", null);
        $this->addBreadcrumb(array("reference"=>"/admin/term/add", "name"=>"Add new term"));
        return $this->view;
    }

    /**
    * This action presents a modify form for Term object with a given id
    * Upon POST the form is processed and saved
    */
    public function modifyAction()
    {
        $id = (int) $this->getParam('id', 0);
        if(!$id)
        {
            $this->setErrorNoParam($this->NO_ID);
            return $this->redirect()->toRoute('admin', array('controller' => 'term'));
        }
        try
        {
            $term = $this->getTable("term")->getTerm($id);
            $this->view->term = $term;
            $this->addBreadcrumb(array("reference"=>"/admin/term/modify/id/{$term->id}", "name"=>"Modify term &laquo;{$term->name}&raquo;"));
            $this->showForm("Modify", $term);
        }
        catch(\Exception $ex)
        {
            $this->setErrorNoParam("Term not found");
            return $this->redirect()->toRoute('admin', array('controller' => 'term'));
        }
        return $this->view;
    }

    /**
     * This is common function used by add and modify actions (to avoid code duplication)
     *
     * @param String $label
     * @param null|Term $term
     */
    public function showForm($label = 'Add', $term = null)
    {
        if($term == null)
        {
            $term = new Term();
        }

        $form = new TermForm($term, $this->getTable("termcategory")->fetchList());
        $form->get("submit")->setValue($label);

        $this->view->form = $form;
        if ($this->getRequest()->isPost())
        {
            $form->setInputFilter($term->getInputFilter());
            $form->setData($this->getRequest()->getPost());
            if ($form->isValid())
            {
                $formData = $form->getData();
                $formData["name"] = strtoupper($formData["name"]);
                $term->exchangeArray($formData);
                $this->getTable("term")->saveTerm($term);
                $this->cache->success = "Term &laquo;".$term->toString()."&raquo; was successfully saved";
                $this->view->setTerminal(true);
                return $this->redirect()->toRoute('admin', array('controller' => 'term'));
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
                return $this->redirect()->toRoute('admin', array('controller' => 'term'));
            }
        }
    }

    /**
    * this action deletes a Term object with a provided id
    */
    public function deleteAction()
    {
        $id = (int) $this->getParam('id', 0);
        if(!$id)
        {
            $this->setErrorNoParam($this->NO_ID);
            return $this->redirect()->toRoute('admin', array('controller' => 'term'));
        }
        try
        {
            $this->getTable("term")->deleteTerm($id);
        }
        catch(\Exception $ex)
        {
            $this->setErrorNoParam("Term not found");
            return $this->redirect()->toRoute('admin', array('controller' => 'term'));
        }
        $this->cache->success = "Term was successfully saved";
        return $this->redirect()->toRoute('admin', array('controller' => 'term'));
    }

    public function cloneAction()
    {
        $id = $this->getParam("id", 0);
        $term = $this->getTable("term")->duplicate($id);
        $this->cache->success = "term &laquo;".$term->toString()."&raquo; was successfully cloned";
        $this->redirect()->toUrl("/admin/term");
        return $this->view;
    }
}
