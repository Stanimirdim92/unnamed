<?php

/**
 * @copyright  2015 (c) Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 *
 * @version    0.0.15
 *
 * @link       TBA
 */

namespace Admin\Controller;

use Admin\Model\User;
use Admin\Form\UserForm;
use Zend\Json\Json;
use Zend\View\Model\JsonModel;
use Admin\Exception\AuthorizationException;

final class UserController extends IndexController
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
     * @param MvcEvent $e
     */
    public function onDispatch(\Zend\Mvc\MvcEvent $e)
    {
        $this->addBreadcrumb(["reference"=>"/admin/user", "name"=>$this->translate("USERS")]);
        parent::onDispatch($e);
    }

    /**
     * This action shows the list with all users.
     *
     * @return ViewModel
     */
    public function indexAction()
    {
        $this->getView()->setTemplate("admin/user/index");
        $paginator = $this->getTable("user")->fetchList(true, [], ["deleted" => 0], null, null, "id DESC");
        $paginator->setCurrentPageNumber((int)$this->getParam("page", 1));
        $paginator->setItemCountPerPage($this->systemSettings('posts', 'user'));
        $this->getView()->paginator = $paginator;
        return $this->getView();
    }

    /**
     * This action presents a modify form for User object with a given id.
     * Upon POST the form is processed and saved.
     *
     * @return ViewModel
     */
    protected function modifyAction()
    {
        $this->getView()->setTemplate("admin/user/modify");
        $user = $this->getTable("user")->getUser((int)$this->getParam("id", 0))->current();
        $this->getView()->user = $user;
        $this->addBreadcrumb(["reference"=>"/admin/user/modify/{$user->getId()}", "name"=> $this->translate("MODIFY_USER")." &laquo;".$user->getName()."&raquo;"]);
        $this->initForm($this->translate("MODIFY_USER"), $user);
        return $this->getView();
    }

    /**
     * This is common function used by add and modify actions (to avoid code duplication).
     *
     * @param string $label
     * @param User $user
     */
    private function initForm($label= '', User $user = null)
    {
        if (!$user instanceof User) {
            throw new AuthorizationException($this->translate("ERROR_AUTHORIZATION"));
        }

        $form = $this->userForm;
        $form->get("submit")->setValue($label);
        $form->bind($user);
        $this->getView()->form = $form;

        if ($this->getRequest()->isPost()) {
            $form->setInputFilter($form->getInputFilter());
            $form->setData($this->getRequest()->getPost());
            if ($form->isValid()) {
                $formData = $form->getData();
                $existingEmail = $this->getTable("user")->fetchList(false, ["email"], ["email" => $formData->getEmail()]);
                if (count($existingEmail) > 1) {
                    $this->setLayoutMessages($this->translate("EMAIL_EXIST")." <b>".$formData->getEmail()."</b> ".$this->translate("ALREADY_EXIST"), 'info');
                } else {
                    $this->getTable("user")->saveUser($user);
                    $this->setLayoutMessages("&laquo;".$user->getFullName()."&raquo; ".$this->translate("SAVE_SUCCESS"), "success");
                }
            } else {
                $this->setLayoutMessages($form->getMessages(), 'error');
            }
        }
    }

    /**
     * @return ViewModel
     */
    protected function disabledAction()
    {
        $this->getView()->setTemplate("admin/user/disabled");
        $paginator = $this->getTable("user")->fetchList(true, [], ["deleted" => 1], null, null, "id DESC");
        $paginator->setCurrentPageNumber((int)$this->getParam("page", 1));
        $paginator->setItemCountPerPage($this->systemSettings('posts', 'user'));
        $this->getView()->paginator = $paginator;
        return $this->getView();
    }

    /**
     * In case that a user account has been disabled and it needs to be enabled call this action.
     */
    protected function enableAction()
    {
        $this->getTable("user")->toggleUserState((int)$this->getParam("id", 0), 0);
        $this->setLayoutMessages($this->translate("USER_ENABLE_SUCCESS"), "success");
    }

    /**
     * Instead if deleting a user account from the database, we simply disabled it.
     */
    protected function disableAction()
    {
        $this->getTable("user")->toggleUserState((int)$this->getParam("id", 0), 1);
        $this->setLayoutMessages($this->translate("USER_DISABLE_SUCCESS"), "success");
    }

    /**
     * this action shows user details from the provided id.
     *
     * @return ViewModel
     */
    protected function detailAction()
    {
        $this->getView()->setTemplate("admin/user/detail");
        $user = $this->getTable("user")->getUser((int)$this->getParam("id", 0))->current();
        $this->getView()->user = $user;
        $this->addBreadcrumb(["reference"=>"/admin/user/detail/".$user->getId()."", "name"=>"&laquo;". $user->getFullName()."&raquo; ".$this->translate("DETAILS")]);
        return $this->getView();
    }

    /**
     * return the list of users that match a given criteria.
     *
     * @return JsonModel
     */
    protected function searchAction()
    {
        $search = (string) $this->getParam('ajaxsearch', null);
        if (isset($search)) {
            if ($this->getRequest()->isXmlHttpRequest()) {
                $this->getView()->setTerminal(true);
                $where = "`name` LIKE '%{$search}%' OR `surname` LIKE '%{$search}%' OR `email` LIKE '%{$search}%' OR `registered` LIKE '%{$search}%'";
                $results = $this->getTable("user")->fetchList(false, ["id", "name", "email", "deleted"], $where, "OR", null, "id DESC");

                $json = [];
                $success = false;

                if ($results) {
                    foreach ($results as $key => $result) {
                        $json[$key]["id"] = $result->getId();
                        $json[$key]["name"] = $result->getName();
                        $json[$key]["email"] = $result->getEmail();
                        $json[$key]["buttons"] = $this->htmlButtons($result->getId(), $result->getFullName(), $result->getDeleted());
                    }
                    $success = true;
                }

                return new JsonModel([
                    'ajaxsearch' =>  Json::encode($json),
                    'statusType' => $success,
                ]);
            }
        }
    }

    /**
     * Used to generade buttons for every user row
     *
     * @method htmlButtons
     *
     * @param int $id
     * @param string $fullName
     * @param int $userStatus
     *
     * @return string
     */
    private function htmlButtons($id, $fullName, $userStatus)
    {
        $action = 'disable';
        $class = 'delete';
        $i18n = "DISABLE";
        if ($userStatus === 1) {
            $action = 'enable';
            $class = 'enable';
            $i18n = "ENABLE";
        }

        return "<li class='table-cell flex-b'>
                <a title='{$this->translate('DETAILS')}' class='btn blue btn-sm' href='/admin/user/detail/{$id}'><i class='fa fa-info'></i></a>
            </li>
            <li class='table-cell flex-b'>
                <a title='{$this->translate('MODIFY_USER')}' href='/admin/user/modify/{$id}' class='btn btn-sm orange'><i class='fa fa-pencil'></i></a>
            </li>
            <li class='table-cell flex-b'>
                <button role='button' aria-pressed='false' aria-label='{$this->translate("$i18n")}' id='{$id}' type='button' class='btn btn-sm {$class} dialog_delete' title='{$this->translate("$i18n")}'><i class='fa fa-trash-o'></i></button>
                <div role='alertdialog' aria-labelledby='dialog{$id}Title' class='delete_{$id} dialog_hide'>
                   <p id='dialog{$id}Title'>{$this->translate("$i18n".'_CONFIRM_TEXT')} &laquo;{$fullName}&raquo;</p>
                    <ul>
                        <li>
                            <a class='btn {$class}' href='/admin/user/{$action}/{$id}'><i class='fa fa-trash-o'></i> {$this->translate("$i18n")}</a>
                        </li>
                        <li>
                            <button role='button' aria-pressed='false' aria-label='{$this->translate('CANCEL')}' type='button' title='{$this->translate('CANCEL')}' class='btn btn-default cancel'><i class='fa fa-times'></i> {$this->translate('CANCEL')}</button>
                        </li>
                    </ul>
                </div>
            </li>";
    }
}
