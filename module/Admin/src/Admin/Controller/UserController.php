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
 * @author     Stanimir Dimitrov <stanimirdim92@gmail.com>
 * @copyright  2015 (c) Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 * @version    0.0.6
 * @link       TBA
 */

namespace Admin\Controller;

use Admin\Model\User;
use Admin\Form\UserForm;
use Zend\Json\Json;
use Zend\View\Model\JsonModel;
use Admin\Exception\AuthorizationException;

class UserController extends IndexController
{
    /**
     * @var UserForm $userForm
     */
    private $userForm = null;

    /**
     * @param UserForm $userForm
     */
    public function __construct(UserForm $userForm = null)
    {
        parent::__construct();
        $this->userForm = $userForm;
    }

    /**
     * Initialize any variables before controller actions
     *
     * @param MvcEvent $e
     */
    public function onDispatch(\Zend\Mvc\MvcEvent $e)
    {
        $this->addBreadcrumb(["reference"=>"/admin/user", "name"=>$this->translate("USERS")]);
        parent::onDispatch($e);
    }

    /**
     * This action shows the list with all users
     */
    public function indexAction()
    {
        $this->view->setTemplate("admin/user/index");
        $paginator = $this->getTable("user")->fetchList(true, [], ["deleted" => 0], null, null, "id DESC");
        $paginator->setCurrentPageNumber((int)$this->getParam("page", 1));
        $paginator->setItemCountPerPage(50);
        $this->view->paginator = $paginator;
        return $this->view;
    }

    /**
     * This action presents a modify form for User object with a given id
     * Upon POST the form is processed and saved
     */
    protected function modifyAction()
    {
        $this->view->setTemplate("admin/user/modify");
        $user = $this->getTable("user")->getUser($this->getParam("id", 0))->current();
        $this->view->user = $user;
        $this->addBreadcrumb(["reference"=>"/admin/user/modify/{$user->getId()}", "name"=> $this->translate("MODIFY_USER")." &laquo;".$user->getName()."&raquo;"]);
        $this->initForm($this->translate("MODIFY_USER"), $user);
        return $this->view;
    }

    /**
     * This is common function used by add and modify actions (to avoid code duplication)
     *
     * @param String $label
     * @param User $user
     */
    private function initForm($label= '' , User $user = null)
    {
        if (!$user instanceof User) {
            throw new AuthorizationException($this->translate("ERROR_AUTHORIZATION"));
        }

        $form = $this->userForm;
        $form->get("submit")->setValue($label);
        $form->bind($user);
        $this->view->form = $form;

        if ($this->getRequest()->isPost()) {
            $form->setInputFilter($form->getInputFilter());
            $form->setData($this->getRequest()->getPost());
            if ($form->isValid()) {
                $formData = $form->getData();
                $existingEmail = $this->getTable("user")->fetchList(false, ["email"], ["email" => $formData->email]);
                if (count($existingEmail) > 1) {
                    $this->setLayoutMessages($this->translate("EMAIL_EXIST")." <b>".$formData->email."</b> ".$this->translate("ALREADY_EXIST"), 'info');
                } else {
                    $this->getTable("user")->saveUser($user);
                    $this->setLayoutMessages("&laquo;".$user->getFullName()."&raquo; ".$this->translate("SAVE_SUCCESS"), "success");
                }
            } else {
                $this->setLayoutMessages($form->getMessages(), 'error');
            }
            return $this->redirect()->toRoute('admin', ['controller' => 'user']);
        }
    }

    protected function disabledAction()
    {
        $this->view->setTemplate("admin/user/disabled");
        $paginator = $this->getTable("user")->fetchList(true, [], ["deleted" => 1], null, null, "id DESC");
        $paginator->setCurrentPageNumber((int)$this->getParam("page", 1));
        $paginator->setItemCountPerPage(50);
        $this->view->paginator = $paginator;
        return $this->view;
    }

    /**
     * In case that a user account has been disabled and it needs to be enabled call this action
     */
    protected function enableAction()
    {
        $user = $this->getTable("user")->toggleUserState($this->getParam("id", 0), 0);
        $this->setLayoutMessages($this->translate("USER_ENABLE_SUCCESS"), "success");
        return $this->redirect()->toRoute('admin', ['controller' => 'user']);
    }

    /**
     * Instead if deleting a user account from the database, we simply disabled it
     */
    protected function disableAction()
    {
        $user = $this->getTable("user")->toggleUserState($this->getParam("id", 0), 1);
        $this->setLayoutMessages($this->translate("USER_DISABLE_SUCCESS"), "success");
        return $this->redirect()->toRoute('admin', ['controller' => 'user']);
    }

    /**
     * this action shows user details from the provided id
     */
    protected function detailAction()
    {
        $this->view->setTemplate("admin/user/detail");
        $user = $this->getTable("user")->getUser($this->getParam("id", 0))->current();
        $this->view->user = $user;
        $this->addBreadcrumb(["reference"=>"/admin/user/detail/".$user->getId()."", "name"=>"&laquo;". $user->getFullName()."&raquo; ".$this->translate("DETAILS")]);
        return $this->view;
    }

    /**
     * return the list of users that match a given criteria
     *
     * @return JsonModel
     */
    protected function searchAction()
    {
        $search = (string) $this->getParam('usersearch', null);
        if (isset($search)) {
            if ($this->getRequest()->isXmlHttpRequest()) {
                $this->view->setTerminal(true);
                $where = "`name` LIKE '%{$search}%' OR `surname` LIKE '%{$search}%' OR `email` LIKE '%{$search}%' OR `registered` LIKE '%{$search}%'";
                $results = $this->getTable("user")->fetchList(false, [], $where, "OR", null, "id DESC");

                $json = [];
                foreach ($results as $result) {
                    $json[] = Json::encode($result);
                }

                return new JsonModel([
                    'usersearch' => $json,
                    'cancel' => $this->translate("CANCEL"),
                    'deleteuser' => $this->translate("DELETE"),
                    'modify' => $this->translate("MODIFY_USER"),
                    'details' => $this->translate("DETAILS"),
                    'delete_text' => $this->translate("DELETE_CONFIRM_TEXT"),
                ]);
            }
        }
    }

    /**
     * This method exports all users from the database in excel format
     *
     * @see  Admin\Model\UserTable::export for more info
     */
    protected function exportAction()
    {
        $filesPath = "public/userfiles/userExports/";
        if (!is_dir($filesPath)) {
            mkdir($filesPath);
        }
        $fileName = $this->getTable("user")->export($filesPath);
        return $this->redirect()->toUrl("/userfiles/userExports/".$fileName);
    }
}
