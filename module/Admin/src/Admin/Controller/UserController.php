<?php

/**
 * @copyright  2015 (c) Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 *
 * @version    0.0.21
 *
 * @link       TBA
 */

namespace Admin\Controller;

use Admin\Entity\User;
use Admin\Form\UserForm;
use Zend\Json\Json;
use Zend\View\Model\JsonModel;
use Admin\Exception\AuthorizationException;
use Zend\Mvc\MvcEvent;

final class UserController extends BaseController
{
    /**
     * @var UserForm
     */
    private $userForm;

    /**
     * @var \Admin\Model\UserTable
     */
    private $userTable;

    /**
     * @param UserForm $userForm
     */
    public function __construct(UserForm $userForm)
    {
        parent::__construct();
        $this->userForm = $userForm;
    }

    /**
     * @param MvcEvent $event
     *
     * @return mixed|void
     */
    public function onDispatch(MvcEvent $event)
    {
        $this->addBreadcrumb(["reference"=>"/admin/user", "name"=>$this->translate("USERS")]);
        $this->userTable = $this->getTable("Admin\Model\UserTable");

        parent::onDispatch($event);
    }

    /**
     * This action shows the list with all users.
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function indexAction()
    {
        $this->getView()->setTemplate("admin/user/index");
        $table = $this->userTable;
        $query = $table->queryBuilder()
                   ->select(["u"])
                   ->from('Admin\Entity\User', 'u')
                   ->where("u.isDisabled = 0");

        $paginator = $table->preparePagination($query, false);
        $paginator->setCurrentPageNumber((int)$this->getParam("page", 1));
        $paginator->setItemCountPerPage($this->systemSettings('posts', 'user'));
        $this->getView()->paginator = $paginator;

        return $this->getView();
    }

    /**
     * This action presents a edit form for User object with a given id.
     * Upon POST the form is processed and saved.
     *
     * @return \Zend\View\Model\ViewModel
     */
    protected function editAction()
    {
        $this->getView()->setTemplate("admin/user/edit");
        $user = $this->userTable->getUser((int)$this->getParam("id", 0));
        $this->getView()->user = $user;
        $this->addBreadcrumb(["reference"=>"/admin/user/edit/{$user->getId()}", "name"=> $this->translate("EDIT_USER")." &laquo;".$user->getName()."&raquo;"]);
        $this->initForm($user);

        return $this->getView();
    }

    /**
     * This is common function used by add and edit actions (to avoid code duplication).
     *
     * @param User $user
     */
    private function initForm(User $user = null)
    {
        if (!$user instanceof User) {
            throw new AuthorizationException($this->translate("ERROR_AUTHORIZATION"));
        }

        $form = $this->userForm;
        $form->bind($user);
        $this->getView()->form = $form;

        if ($this->getRequest()->isPost()) {
            $form->setInputFilter($form->getInputFilter());
            $form->setData($this->getRequest()->getPost());
            if ($form->isValid()) {
                $formData = $form->getData();

                // check for existing email
                $query = $this->userTable;
                $existingEmail = $query->queryBuilder()
                   ->select(["u"])
                   ->from('Admin\Entity\User', 'u')
                   ->where("u.email = :email")
                   ->setParameter(":email", (string) $formData->getEmail())->getQuery()->getResult();

                if (count($existingEmail) > 1) {
                    return $this->setLayoutMessages($this->translate("EMAIL_EXIST")." <b>".$formData->getEmail()."</b> ".$this->translate("ALREADY_EXIST"), 'info');
                }

                $this->userTable->saveUser($user);
                return $this->setLayoutMessages("&laquo;".$user->getFullName()."&raquo; ".$this->translate("SAVE_SUCCESS"), "success");

            }
            return $this->setLayoutMessages($form->getMessages(), 'error');
        }
    }

    /**
     * @return \Zend\View\Model\ViewModel
     */
    protected function disabledAction()
    {
        $this->getView()->setTemplate("admin/user/disabled");

        $query = $this->userTable;
        $q = $query->queryBuilder()
                   ->select(["u"])
                   ->from('Admin\Entity\User', 'u')
                   ->where("u.isDisabled = 1");

        $paginator = $query->preparePagination($q, false);
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
        $this->userTable->toggleUserState((int)$this->getParam("id", 0), 0);
        $this->setLayoutMessages($this->translate("USER_ENABLE_SUCCESS"), "success");
    }

    /**
     * Instead if deleting a user account from the database, we simply disabled it.
     */
    protected function disableAction()
    {
        $this->userTable->toggleUserState((int)$this->getParam("id", 0), 1);
        $this->setLayoutMessages($this->translate("USER_DISABLE_SUCCESS"), "success");
    }

    /**
     * this action shows user details from the provided id.
     *
     * @return \Zend\View\Model\ViewModel
     */
    protected function detailAction()
    {
        $this->getView()->setTemplate("admin/user/detail");
        $user = $this->userTable->getUser((int)$this->getParam("id", 0));
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
        if (isset($search) && $this->getRequest()->isXmlHttpRequest()) {
                $this->getView()->setTerminal(true);
                $queryBuilder = $this->userTable->queryBuilder();
                $results = $queryBuilder->select(["u"])
                    ->from('Admin\Entity\User', 'u')
                    ->where('u.name = :name')
                    ->orWhere('u.surname LIKE :surname')
                    ->orWhere('u.email LIKE :email')
                    ->setParameter(':name', (string) $search)
                    ->setParameter(':surname', (string) $search)
                    ->setParameter(':email', (string) $search)
                    ->getQuery()
                    ->getResult();

                $json = [];
                $success = false;

                if (!empty($results)) {
                    foreach ($results as $key => $result) {
                        $json[$key]["id"] = $result->getId();
                        $json[$key]["name"] = $result->getName();
                        $json[$key]["email"] = $result->getEmail();
                        $json[$key]["buttons"] = $this->htmlButtons($result->getId(), $result->getFullName(), $result->isDisabled());
                    }
                    $success = true;
                }

                return new JsonModel(
                    [
                    'ajaxsearch' =>  Json::encode($json),
                    'statusType' => $success,
                    ]
                );
        }
    }

    /**
     * Used to generate buttons for every user row
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
                <a title='{$this->translate('EDIT_USER')}' href='/admin/user/edit/{$id}' class='btn btn-sm orange'><i class='fa fa-pencil'></i></a>
            </li>
            <li class='table-cell flex-b'>
                <button role='button' aria-pressed='false' aria-label='{$this->translate("$i18n")}' id='{$id}' type='button' class='btn btn-sm {$class} dialog_delete' title='{$this->translate("$i18n")}'><i class='fa fa-times'></i></button>
                <div role='alertdialog' aria-labelledby='dialog{$id}Title' class='delete_{$id} dialog_hide'>
                   <p id='dialog{$id}Title'>{$this->translate("$i18n".'_CONFIRM_TEXT')} &laquo;{$fullName}&raquo;</p>
                    <ul>
                        <li>
                            <a class='btn {$class}' href='/admin/user/{$action}/{$id}'><i class='fa fa-times'></i> {$this->translate("$i18n")}</a>
                        </li>
                        <li>
                            <button role='button' aria-pressed='false' aria-label='{$this->translate('CANCEL')}' type='button' title='{$this->translate('CANCEL')}' class='btn btn-default cancel'><i class='fa fa-times'></i> {$this->translate('CANCEL')}</button>
                        </li>
                    </ul>
                </div>
            </li>";
    }
}
