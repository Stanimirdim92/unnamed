<?php

/**
 * @copyright  2015 (c) Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 *
 * @version    0.0.18
 *
 * @link       TBA
 */

namespace Admin\Controller;

use Admin\Model\Administrator;
use Admin\Form\AdministratorForm;
use Zend\Json\Json;
use Zend\View\Model\JsonModel;

final class AdministratorController extends BaseController
{
    /**
     * @var AdministratorForm $administratorForm
     */
    private $administratorForm = null;

    /**
     * @param AdministratorForm $administratorForm
     */
    public function __construct(AdministratorForm $administratorForm = null)
    {
        parent::__construct();

        $this->administratorForm = $administratorForm;
    }

    /**
     * @param MvcEvent $e
     */
    public function onDispatch(\Zend\Mvc\MvcEvent $e)
    {
        $this->addBreadcrumb(["reference"=>"/admin/administrator", "name"=>$this->translate("ADMINISTRATORS")]);
        parent::onDispatch($e);
    }

    /**
     * This action shows the list of all Administrators.
     */
    public function indexAction()
    {
        $this->getView()->setTemplate("admin/administrator/index");
        $paginator = $this->getTable("AdministratorTable");
        $paginator->columns(["user"]);
        $paginator->join("user", "administrator.user=user.id", ["name"], "left");
        $paginator = $paginator->fetchPagination();
        $paginator->setCurrentPageNumber((int)$this->getParam("page", 1));
        $paginator->setItemCountPerPage($this->systemSettings('posts', 'administrator'));
        $this->getView()->paginator = $paginator;
        return $this->getView();
    }

    /**
     * This action serves for adding a new users as administrators.
     */
    protected function addAction()
    {
        $this->getView()->setTemplate("admin/administrator/add");
        $this->initForm($this->translate("ADD_ADMINISTRATOR"), null);
        $this->addBreadcrumb(["reference"=>"/admin/administrator/add", "name"=>$this->translate("ADD_ADMINISTRATOR")]);
        return $this->getView();
    }

    /**
     * This action presents a edit form for Administrator object with a given id.
     * Upon POST the form is processed and saved.
     *
     * @return ViewModel
     */
    protected function editAction()
    {
        $this->getView()->setTemplate("admin/administrator/edit");
        $administrator = $this->getTable("AdministratorTable")->getAdministrator((int) $this->getParam("id", 0));
        $this->getView()->administrator = $administrator;
        $this->addBreadcrumb(["reference"=>"/admin/administrator/edit/{$administrator->getUser()}", "name"=>$this->translate("EDIT_ADMINISTRATOR")]);
        $this->initForm($this->translate("EDIT_ADMINISTRATOR"), $administrator);
        return $this->getView();
    }

    /**
     * this action deletes a administrator
     */
    protected function deleteAction()
    {
        $id = (int)$this->getParam('id', 0);
        $userTable = $this->getTable("UserTable");
        $user = $userTable->getUser($id);
        $user->setAdmin(0);
        $userTable->saveUser($user);
        $this->getTable("AdministratorTable")->deleteAdministrator($id);
        $this->setLayoutMessages($this->translate("DELETE_ADMINISTRATOR_SUCCESS"), "success");
    }

    /**
     * This action is used in combination with the javascript ajax function
     * to search for existing users and add them as administrators.
     *
     * @return JsonModel
     */
    protected function searchAction()
    {
        $search = (string) $this->getParam('ajaxsearch');
        $this->getView()->setTerminal(true);
        if (isset($search) && $this->getRequest()->isXmlHttpRequest()) {
            $results = $this->getTable("UserTable");
            $results->columns(["id", "name", "surname", "email"]);
            $results->where("`name` LIKE '%{$search}%' OR `surname` LIKE '%{$search}%' OR `email` LIKE '%{$search}%'");
            $results = $results->fetch();

            $json = [];
            $success = false;
            if ($results) {
                foreach ($results as $key => $result) {
                    $json[$key]["id"] = $result->getId();
                    $json[$key]["name"] = $result->getName();
                    $json[$key]["surname"] = $result->getSurname();
                    $json[$key]["email"] = $result->getEmail();
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
     * This is common function used by add and edit actions (to avoid code duplication).
     *
     * @param String $label
     * @param Administrator $administrator
     */
    private function initForm($label = '', Administrator $administrator = null)
    {
        if (!$administrator instanceof Administrator) {
            $administrator = new Administrator([]);
        }

        /**
         * @var $form AdministratorForm
         */
        $form = $this->administratorForm;
        $form->get("submit")->setValue($label);
        $form->bind($administrator);
        $this->getView()->form = $form;

        if ($this->getRequest()->isPost()) {
            $form->setInputFilter($form->getInputFilter());
            $form->setData($this->getRequest()->getPost());
            if ($form->isValid()) {
                $formData = $form->getData();
                $user = $this->getTable("UserTable")->getUser($formData->getUser());
                // valid user id
                if (count($user) === 1) {
                    // should return false|null|0 etc.
                    $adminExist = $this->getTable("AdministratorTable")->getAdministrator($user->getId());

                    /*
                     * See if user is in admin table, but admin column from user table is 0.
                     * If this is the case remove all access
                     */
                    if (($adminExist && (int) $user->getAdmin() === 0) ||
                         (!$adminExist && (int) $user->getAdmin() > 0)
                       ) {
                        $user->setAdmin(0);
                        $this->getTable("UserTable")->saveUser($user);
                        $this->getTable("AdministratorTable")->deleteAdministrator($user->getId());
                        return $this->setLayoutMessages("&laquo;".$user->getName()."&raquo; ".$this->translate("ERROR"), 'error');
                    }

                    /*
                     * If user is not in admin table and user.admin column is 0
                     */
                    if (!$adminExist && (int) $user->getAdmin() === 0) {
                        $user->setAdmin(1);
                        $this->getTable("UserTable")->saveUser($user);
                        $this->getTable("AdministratorTable")->saveAdministrator($administrator);
                        return $this->setLayoutMessages("&laquo;".$user->getName()."&raquo; ".$this->translate("SAVE_SUCCESS"), 'success');
                    }
                    return $this->setLayoutMessages($user->getName().$this->translate("ALREADY_ADMIN"), 'info');
                }
                return $this->setLayoutMessages($this->translate("ERROR"), 'error');
            }
            return $this->setLayoutMessages($form->getMessages(), 'error');
        }
    }
}
