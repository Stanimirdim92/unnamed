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

use Admin\Entity\Administrator;
use Admin\Form\AdministratorForm;
use Zend\Json\Json;
use Zend\View\Model\JsonModel;

final class AdministratorController extends BaseController
{
    /**
     * @var AdministratorForm
     */
    private $administratorForm;

    /**
     * @var Administrator
     */
    private $administratorTable;


    /**
     * @param AdministratorForm $administratorForm
     */
    public function __construct(AdministratorForm $administratorForm)
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
        $this->administratorTable = $this->getTable("Admin\Model\AdministratorTable");

        parent::onDispatch($e);
    }

    /**
     * This action shows the list of all Administrators.
     */
    public function indexAction()
    {
        $this->getView()->setTemplate("admin/administrator/index");
        $query = $this->administratorTable->queryBuilder()->getEntityManager();
        $query = $query->createQuery("SELECT a.user, u.name FROM Admin\Entity\Administrator AS a LEFT JOIN Admin\Entity\User AS u WITH a.user=u.id");

        $this->getView()->paginator = $query->getResult();

        return $this->getView();
    }

    /**
     * This action serves for adding a new users as administrators.
     */
    protected function addAction()
    {
        $this->getView()->setTemplate("admin/administrator/add");
        $this->initForm(null);
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
        $administrator = $this->administratorTable->getAdministrator((int) $this->getParam("id", 0));
        $this->getView()->administrator = $administrator;
        $this->addBreadcrumb(["reference"=>"/admin/administrator/edit/{$administrator->getUser()}", "name"=>$this->translate("EDIT_ADMINISTRATOR")]);
        $this->initForm($administrator);

        return $this->getView();
    }

    /**
     * this action deletes a administrator
     */
    protected function deleteAction()
    {
        $id = (int)$this->getParam('id', 0);
        $userTable = $this->getTable("Admin\Model\UserTable");
        $user = $userTable->getUser($id);
        $user->setAdmin(0);
        $userTable->saveUser($user);
        $this->administratorTable->deleteAdministrator($id);
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
        if (isset($search) && $this->getRequest()->isXmlHttpRequest()) {
                $this->getView()->setTerminal(true);
                $queryBuilder = $this->getTable("Admin\Model\UserTable")->queryBuilder();
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
     * @param Administrator $administrator
     */
    private function initForm(Administrator $administrator = null)
    {
        if (!$administrator instanceof Administrator) {
            $administrator = new Administrator([]);
        }

        /**
         * @var $form AdministratorForm
         */
        $form = $this->administratorForm;
        $form->bind($administrator);
        $this->getView()->form = $form;

        if ($this->getRequest()->isPost()) {
            $form->setInputFilter($form->getInputFilter());
            $form->setData($this->getRequest()->getPost());
            if ($form->isValid()) {
                $formData = $form->getData();
                $user = $this->getTable("Admin\Model\UserTable")->getUser($formData->getUser());
                // valid user id
                if (count($user) === 1) {
                    // should return false|null|0 etc.
                    $adminExist = $this->administratorTable->getAdministrator($user->getId());

                    /*
                     * See if user is in admin table, but admin column from user table is 0.
                     * If this is the case remove all access
                     */
                    if (($adminExist && (int) $user->getAdmin() === 0) ||
                         (!$adminExist && (int) $user->getAdmin() > 0)
                       ) {
                        $user->setAdmin(0);
                        $this->getTable("Admin\Model\UserTable")->saveUser($user);
                        $this->administratorTable->deleteAdministrator($user->getId());
                        return $this->setLayoutMessages("&laquo;".$user->getName()."&raquo; ".$this->translate("ERROR"), 'error');
                    }

                    /*
                     * If user is not in admin table and user.admin column is 0
                     */
                    if (!$adminExist && (int) $user->getAdmin() === 0) {
                        $user->setAdmin(1);
                        $this->getTable("Admin\Model\UserTable")->saveUser($user);
                        $this->getTable("Admin\Model\AdministratorTable")->saveAdministrator($administrator);
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
