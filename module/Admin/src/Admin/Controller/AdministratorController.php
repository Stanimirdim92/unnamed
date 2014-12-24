<?php
namespace Admin\Controller;

use Zend\File\Transfer\Adapter\Http;

use Admin\Controller\IndexController;
use Admin\Model\Administrator;
use Admin\Form\AdministratorForm;
use Admin\Form\AdministratorSearchForm;

use Custom\Error\AuthorizationException;

class AdministratorController extends IndexController
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
        $this->addBreadcrumb(array("reference"=>"/admin/administrator", "name"=>"Administrators"));
        parent::onDispatch($e);
    }

    /**
     * This action shows the list of all (or filtered) Administrator objects
     */
    public function indexAction()
    {
        $order = "id DESC";
        $paginator = $this->getTable("administrator")->fetchList(true, null, $order);
        $paginator->setCurrentPageNumber((int)$this->params("page",1));
        $paginator->setItemCountPerPage(15);
        $this->view->paginator = $paginator;
        return $this->view;
    }

    /**
     * This action serves for adding a new object of type UserClass
     */
    public function addAction()
    {
        $this->showForm("Add", null);
        $this->addBreadcrumb(array("reference"=>"/admin/administrator/add", "name"=>"Add new administrator"));
        return $this->view;
    }

    /**
     * This action presents a modify form for Administrator object with a given id
     * Upon POST the form is processed and saved
     */
    public function modifyAction()
    {
        $id = (int) $this->getParam("id", 0);
        if(!$id)
        {
            $this->errorNoParam($this->NO_ID);
            return $this->redirect()->toRoute('admin', array('controller' => 'administrator'));
        }
        try
        {
            $administrator = $this->getTable("administrator")->getAdministrator($id);
            $this->view->administrator = $administrator;
            $this->addBreadcrumb(array("reference"=>"/admin/administrator/modify/id/{$administrator->id}", "name"=>"Modify administrator"));
            $this->showForm("Modify", $administrator);
        }
        catch(\Exception $ex)
        {
            $this->errorNoParam("Administrator not found");
            return $this->redirect()->toRoute('admin', array('controller' => 'administrator'));
        }
        return $this->view;
    }

    /**
     * This is common function used by add and modify actions (to avoid code duplication)
     *
     * @param String $label
     * @param null|Administrator $administrator
     */
    public function showForm($label='Add', $administrator=null)
    {
        if($administrator==null) $administrator = new Administrator();

        $form = new AdministratorForm($administrator);
        $form->get("submit")->setValue($label);
        $this->view->form = $form;
        if($this->getRequest()->isPost())
        {
            $form->setInputFilter($administrator->getInputFilter());
            $form->setData(array_merge_recursive($this->getRequest()->getPost()->toArray(),$this->getRequest()->getFiles()->toArray()));
            if($form->isValid())
            {
                $formData = $form->getData();

                $user = $this->getTable("user")->getUser($formData['user']);
                // valid user id
                if (sizeof($user) == 1)
                {
                    $adminExist = $this->getTable("administrator")->fetchList(false, "user='{$user->id}'");
                    if (sizeof($adminExist) > 0)
                    {
                        $this->cache->error = $user->toString()." is already administrator";
                        $this->view->setTerminal(true);
                        return $this->redirect()->toRoute('admin', array('controller' => 'administrator'));
                    }
                    else
                    {
                        $user->setAdmin(1);
                        $administrator->exchangeArray($formData);
                        $this->getTable("user")->saveUser($user);
                        $this->getTable("administrator")->saveAdministrator($administrator);
                        $this->cache->success = "Administrator was successfully saved";
                        $this->view->setTerminal(true);
                        return $this->redirect()->toRoute('admin', array('controller' => 'administrator'));
                    }
                }
                else
                {
                    $this->errorNoParam($this->NO_ID);
                    return $this->redirect()->toRoute('admin', array('controller' => 'administrator'));
                }
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
                $this->errorNoParam($error);
                return $this->redirect()->toRoute('admin', array('controller' => 'administrator'));
            }
        }
    }

    /**
     * this action deletes a administrator object with a provided id
     */
    public function deleteAction()
    {
        $id = (int) $this->getParam('id', 0);
        if(!$id)
        {
            $this->errorNoParam($this->NO_ID);
            return $this->redirect()->toRoute('admin', array('controller' => 'administrator'));
        }
        try
        {
            $administrator = $this->getTable("administrator")->getAdministrator($id);
            $user = $this->getTable("user")->getUser($administrator->user);
            $user->setAdmin(0);
            $this->getTable("user")->saveUser($user);
            $this->getTable("administrator")->deleteAdministrator($id);
        }
        catch(\Exception $ex)
        {
            $this->errorNoParam("Administrator not found");
            return $this->redirect()->toRoute('admin', array('controller' => 'administrator'));
        }
        $this->cache->success = "Administrator was successfully deleted";
        return $this->redirect()->toRoute('admin', array('controller' => 'administrator'));
    }

    public function detailAction()
    {
        $id = (int) $this->getParam('id', 0);
        if(!$id)
        {
            $this->errorNoParam($this->NO_ID);
            return $this->redirect()->toRoute('admin', array('controller' => 'administrator'));
        }
        try
        {
            $administrator = $this->getTable("administrator")->getAdministrator($id);
            $this->view->administrator = $administrator;
        }
        catch(\Exception $ex)
        {
            $this->errorNoParam("Administrator not found");
            return $this->redirect()->toRoute('admin', array('controller' => 'administrator'));
        }
        $this->addBreadcrumb(array("reference"=>"/admin/administrator/detail/id/{$administrator->id}", "name"=>" Administrator details"));
        return $this->view;
    }

    protected function cloneAction()
    {
        $id = $this->getParam("id", 0);
        $administrator = $this->getTable("administrator")->duplicate($id);
        $this->cache->success = "Administrator was successfully cloned";
        $this->redirect()->toUrl("/admin/administrator");
        return $this->view;
    }
}
