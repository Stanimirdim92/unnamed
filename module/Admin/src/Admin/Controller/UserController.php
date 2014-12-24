<?php
namespace Admin\Controller;

use Admin\Controller\IndexController;
use Admin\Model\User;
use Admin\Form\UserForm;
use Admin\Form\UserSearchForm;

use Zend\Json\Json;
use Zend\View\Model\JsonModel;

use Custom\Error\AuthorizationException;

class UserController extends IndexController
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
        $this->addBreadcrumb(array("reference"=>"/admin/user", "name"=>"Users"));
        parent::onDispatch($e);
    }

    /**
     * This action shows the list of all (or filtered) User objects
     */
    public function indexAction()
    {
        $search = $this->getParam("search", null);
        $where = "deleted = '0'";
        if($search!=null)
        {
            $where = "(deleted = '0') AND (`name` LIKE '%{$search}%' OR `surname` LIKE '%{$search}%' OR `email` LIKE '%{$search}%' OR `registered` LIKE '%{$search}%' OR `lastLogin` LIKE '%{$search}%')";
        }
        $order = "id DESC";
        $this->customPages(true, $where, $order, 50, $search);
        return $this->view;
    }

    /**
     * This action presents a modify form for User object with a given id
     * Upon POST the form is processed and saved
     */
    public function modifyAction()
    {
        $id = (int) $this->getParam("id", 0);
        if(!$id)
        {
            $this->errorNoParam($this->NO_ID);
            return $this->redirect()->toRoute('admin', array('controller' => 'user'));
        }
        try
        {
            $user = $this->getTable("user")->getUser($id);
            $this->view->user = $user;
            $this->addBreadcrumb(array("reference"=>"/admin/user/modify/id/{$user->id}", "name"=>"Modify user &laquo;".$user->toString()."&raquo;"));
            $this->showForm("Modify", $user);
        }
        catch(\Exception $ex)
        {
            $this->errorNoParam("User not found");
            return $this->redirect()->toRoute('admin', array('controller' => 'user'));
        }
        return $this->view;
    }

    /**
     * This is common function used by add and modify actions (to avoid code duplication)
     *
     * @param String $label
     * @param null|User $user
     */
    public function showForm($label='', $user=null)
    {
        if($user==null) throw new AuthorizationException($this->session->ERROR_AUTHORIZATION);

        $form = new UserForm($user, $this->getTable("language")->fetchList(false, "active = '1'", "name ASC"), $this->getTable("currency")->fetchList(false, "active = '1'", "name ASC"));
        $form->get("submit")->setValue($label);
        $this->view->form = $form;
        $this->view->id = $user->id;
        if($this->getRequest()->isPost())
        {
            $form->setInputFilter($form->getInputFilter());
            $form->setData($this->getRequest()->getPost());
            if($form->isValid())
            {
                $formData = $form->getData();
                $name = str_replace(" ", "_", $formData["name"]);
                $existingUser = $this->getTable("user")->fetchList(false, "name = '{$name}' AND id != '{$user->id}'");
                $existingEmail = $this->getTable("user")->fetchList(false, "email = '".$formData['email']."' AND id != '{$user->id}'");
                (sizeof($existingUser) > 0 ? $this->errorNoParam($this->session->USERNAME_EXIST." <b>{$name}</b> ".$this->session->ALREADY_EXIST) : "");
                (sizeof($existingEmail) > 0 ? $this->errorNoParam($this->session->EMAIL_EXIST." <b>".$formData["email"]."</b> ".$this->session->ALREADY_EXIST) : "");

                if(sizeof($existingEmail) == 0 && sizeof($existingUser) == 0)
                {
                    $user->setName($name);
                    $user->setSurname($formData['surname']);
                    $user->setEmail($formData['email']);
                    // $user->setLanguage($formData["language"]);
                    $user->setAdmin($user->admin);
                    $user->setDeleted($formData["deleted"]);
                    $user->setBirthDate($formData["birthDate"]);
                    $this->getTable("user")->saveUser($user);
                }
                $this->cache->success ="User &laquo;".$user->toString()."&raquo; was successfully saved";
                $this->view->setTerminal(true);
                return $this->redirect()->toRoute('admin', array('controller' => 'user'));
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
                return $this->redirect()->toRoute('admin', array('controller' => 'user'));
            }
        }
    }

    /**
     * this action deletes a user object with a provided id
     */
    public function deleteAction()
    {
        $id = (int) $this->getParam('id', 0);
        if(!$id)
        {
            $this->errorNoParam($this->NO_ID);
            return $this->redirect()->toRoute('admin', array('controller' => 'user'));
        }
        try
        {
            $this->getTable("user")->deleteUser($id);
        }
        catch(\Exception $ex)
        {
            $this->errorNoParam("User not found");
            return $this->redirect()->toRoute('admin', array('controller' => 'user'));
        }
        $this->cache->success = "User was successfully deleted";
        return $this->redirect()->toRoute('admin', array('controller' => 'user'));
    }

    public function disabledAction()
    {
        $search = $this->getParam("search", null);
        $where = "deleted = '1'";
        if($search!=null)
        {
            $where = "(deleted = '1') AND (`name` LIKE '%{$search}%' OR `surname` LIKE '%{$search}%' OR `email` LIKE '%{$search}%' OR `registered` LIKE '%{$search}%' OR `lastLogin` LIKE '%{$search}%')";
        }
        $order = "id DESC";
        $this->customPages(true, $where, $order, 50, $search);
        return $this->view;
    }

    public function detailAction()
    {
        $id = (int) $this->getParam('id', 0);
        if(!$id)
        {
            $this->errorNoParam($this->NO_ID);
            return $this->redirect()->toRoute('admin', array('controller' => 'user'));
        }
        try
        {
            $user = $this->getTable("user")->getUser($id);
            $this->view->user = $user;
        }
        catch(\Exception $ex)
        {
            $this->errorNoParam("User not found");
            return $this->redirect()->toRoute('admin', array('controller' => 'user'));
        }
        $this->addBreadcrumb(array("reference"=>"/admin/user/detail/id/{$user->id}", "name"=>"User &laquo;". $user->toString()."&raquo; details"));
        return $this->view;
    }

    /**
     * return the list of users that match a given criteria
     * @return \Zend\View\Model\JsonModel
     */
    protected function searchAction()
    {
        $request = $this->getRequest();
        $search = $this->params()->fromQuery('usersearch');
        if (isset($search))
        {
            if ($request->isXmlHttpRequest())
            {
                $this->view->setTerminal(true);
                $where = "`name` LIKE '%{$search}%' OR `surname` LIKE '%{$search}%' OR `email` LIKE '%{$search}%' OR `registered` LIKE '%{$search}%' OR `lastLogin` LIKE '%{$search}%'";
                $results = $this->getTable("user")->fetchList(false, $where, "id DESC");
                $json = array();
                foreach ($results as $result)
                {
                    $json[] = Json::encode($result);
                }
                return new JsonModel(array(
                    'usersearch' => $json,
                    'cancel' => "Cancel",
                    'deleteuser' => "delete",
                    'modify' => "modify",
                    'details' => "details",
                    'delete_text' => "Are you sure you would like to delete this user"
                ));
            }
        }
    }

    /**
     * 1 is there so we can get a proper user Object 
     * instead we get a call to undefined function get() in User model
     *
     * @var $users \Admin\Model\User
     */
    protected function exportAction()
    {
        $filesPath = $_SERVER['DOCUMENT_ROOT']."/zend/public/userfiles/userExports/";
        if (!file_exists($filesPath))
        {
            mkdir($filesPath);
        }
        $users = $this->getTable("User")->getUser($this->cache->user->id);
        $fileName = $users->export($filesPath);
        $this->redirect()->toUrl("/userfiles/userExports/".$fileName);
    }

    protected function cloneAction()
    {
        $id = $this->getParam("id");
        $user = $this->getTable("user")->duplicate($id);
        $this->cache->success = "User &laquo;".$user->toString()."&raquo; was successfully cloned";
        $this->redirect()->toUrl("/admin/user");
        return $this->view;
    }

    /**
     * @param null $where
     * @param null order
     * @param Int $itemPerPage
     * @param String $search
     * @return void
     */
    private function customPages($isPagination = true, $where = null, $order = null, $itemPerPage = 50, $search)
    {
        $paginator = $this->getTable("user")->fetchList($isPagination, $where, $order);
        $paginator->setCurrentPageNumber((int)$this->params("page",1));
        $paginator->setItemCountPerPage(50);
        $this->view->paginator = $paginator;
        $form = new UserSearchForm();
        $form->remove("submit");
        $form->get("search")->setValue($search);
        $this->view->form = $form;
    }
}
