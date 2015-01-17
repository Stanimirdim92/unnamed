<?php
namespace Admin\Controller;

use Admin\Controller\IndexController;
use Admin\Model\Language;
use Admin\Form\LanguageForm;
use Admin\Form\LanguageSearchForm;

class LanguageController extends IndexController
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
        $this->addBreadcrumb(array("reference"=>"/admin/language", "name"=>"Languages"));
        parent::onDispatch($e);
    }

    /**
     * This action shows the list of all (or filtered) Language objects
     */
    public function indexAction()
    {
        $search = $this->getParam("search", null);
        $where = null;
        if($search != null)
        {
            $where = "`name` LIKE '%{$search}%'";
        }
        $order = "name ASC";
        $paginator = $this->getTable("language")->fetchList(true, $where, $order);
        $paginator->setCurrentPageNumber((int)$this->params("page",1));
        $paginator->setItemCountPerPage(50);
        $this->view->paginator = $paginator;
        $form = new LanguageSearchForm();
        $form->get("submit")->setValue($this->session->SEARCH);
        $form->get("search")->setValue($search);
        $this->view->form = $form;
        return $this->view;
    }
    
    /**
     * This action serves for adding a new object of type Language
     */
    public function addAction()
    {
        $this->showForm("Language", null);
        $this->addBreadcrumb(array("reference"=>"/admin/language/add", "name"=>"Add new language"));
        return $this->view;
    }

    /**
     * This action presents a modify form for Language object with a given id
     * Upon POST the form is processed and saved
     */
    public function modifyAction()
    {
        $id = $this->getParam("id", 0);
        if(!$id)
        {
            $this->setErrorNoParam($this->NO_ID);
            return $this->redirect()->toRoute('admin', array('controller' => 'language'));
        }
        try
        {
            $language = $this->getTable("language")->getLanguage($id);
            $this->view->language = $language;
            $this->addBreadcrumb(array("reference"=>"/admin/language/modify/id/{$language->id}", "name"=>"Modify language &laquo;".$language->toString()."&raquo;"));
            $this->showForm("Modify", $language);
        }
        catch(\Exception $ex)
        {
            $this->setErrorNoParam("Language not found");
            return $this->redirect()->toRoute('admin', array('controller' => 'language'));
        }
        return $this->view;
    }
    
    /**
     * This is common function used by add and modify actions (to avoid code duplication)
     *
     * @param String $label
     * @param null|Language $language
     */
    public function showForm($label = '', $language = null)
    {
        if($language == null)
        {
            $language = new Language();
        }

        $form = new LanguageForm($language);

        $form->get("submit")->setValue($label);
        $this->view->form = $form;
        if ($this->getRequest()->isPost()) 
        {
            $form->setInputFilter($language->getInputFilter());
            $form->setData($this->getRequest()->getPost());
            if ($form->isValid())
            {
                $language->exchangeArray($form->getData());
                $this->getTable("language")->saveLanguage($language);
                $this->cache->success = $this->session->LANGUAGE."&nbsp;&laquo;".$language->toString()."&raquo; ".$this->session->SAVE_SUCCESS;
                $this->view->setTerminal(true);
                return $this->redirect()->toRoute('admin', array('controller' => 'language'));
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
                return $this->redirect()->toRoute('admin', array('controller' => 'language'));
            }
        }
    }
    
    /**
     * this action deletes a language object with a provided id
     */
    public function deleteAction()
    {
        $id = (int) $this->getParam('id', 0);
        if(!$id)
        {
            $this->setErrorNoParam($this->NO_ID);
            return $this->redirect()->toRoute('admin', array('controller' => 'language'));
        }
        try
        {
            $this->getTable("language")->deleteLanguage($id);
        }
        catch(\Exception $ex)
        {
            $this->setErrorNoParam("Language not found");
            return $this->redirect()->toRoute('admin', array('controller' => 'language'));
        }
        $this->cache->success = "Language was successfully deleted";
        return $this->redirect()->toRoute('admin', array('controller' => 'language'));
    }

    public function detailAction()
    {
        $id = (int) $this->getParam('id', 0);
        if(!$id)
        {
            $this->setErrorNoParam($this->NO_ID);
            return $this->redirect()->toRoute('admin', array('controller' => 'language'));
        }
        try
        {
            $lang = $this->getTable("Language")->getLanguage($id);
            $this->view->lang = $lang;
        }
        catch(\Exception $ex)
        {
            $this->setErrorNoParam("Language not found");
            return $this->redirect()->toRoute('admin', array('controller' => 'language'));
        }
        $this->addBreadcrumb(array("reference"=>"/admin/language/detail/id/{$lang->id}", "name"=>"language &laquo;". $lang->toString()."&raquo; details"));
        return $this->view;
    }

    public function cloneAction()
    {
        $id = $this->getParam("id", 0);
        $language = $this->getTable("language")->duplicate($id);
        $this->cache->success = "Language &laquo;".$language->toString()."&raquo; was successfully cloned";
        $this->redirect()->toUrl("/admin/language");
        return $this->view;
    }
}
