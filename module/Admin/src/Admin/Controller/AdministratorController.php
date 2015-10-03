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

use Admin\Model\Administrator;
use Admin\Form\AdministratorForm;
use Zend\Json\Json;
use Zend\View\Model\JsonModel;

final class AdministratorController extends IndexController
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
        $paginator = $this->getTable("administrator")->fetchJoin(true, "user", ["user"], ["name"], "administrator.user=user.id", "left");
        $paginator->setCurrentPageNumber((int)$this->getParam("page", 1));
        $paginator->setItemCountPerPage(20);
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
     * This action presents a modify form for Administrator object with a given id.
     * Upon POST the form is processed and saved.
     *
     * @return ViewModel
     */
    protected function modifyAction()
    {
        $this->getView()->setTemplate("admin/administrator/modify");
        $administrator = $this->getTable("administrator")->getAdministrator((int) $this->getParam("id", 0))->current();
        $this->getView()->administrator = $administrator;
        $this->addBreadcrumb(["reference"=>"/admin/administrator/modify/{$administrator->getUser()}", "name"=>$this->translate("MODIY_ADMINISTRATOR")]);
        $this->initForm($this->translate("MODIY_ADMINISTRATOR"), $administrator);
        return $this->getView();
    }

    /**
     * this action deletes a administrator
     */
    protected function deleteAction()
    {
        $id = (int)$this->getParam('id', 0);
        $userTable = $this->getTable("user");
        $user = $userTable->getUser($id)->current();
        $user->setAdmin(0);
        $userTable->saveUser($user);
        $this->getTable("administrator")->deleteAdministrator($id);
        $this->setLayoutMessages($this->translate("DELETE_ADMINISTRATOR_SUCCESS"), "success");
        return $this->redirect()->toRoute('admin/default', ['controller' => 'administrator']);
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
            $where = "`name` LIKE '%{$search}%' OR `surname` LIKE '%{$search}%' OR `email` LIKE '%{$search}%'";
            $results = $this->getTable("user")->fetchList(false, ["id", "name", "surname", "email"], $where);

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
            return new JsonModel([
                'ajaxsearch' =>  Json::encode($json),
                'statusType' => $success,
            ]);
        }
    }

    /**
     * This is common function used by add and modify actions (to avoid code duplication).
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
                $user = $this->getTable("user")->getUser($formData->getUser())->current();
                // valid user id
                if (count($user) === 1) {
                    // should return false|null|0 etc.
                    $adminExist = $this->getTable("administrator")->getAdministrator($user->getId());

                    /*
                     * See if user is in admin table, but admin column from user table is 0.
                     * If this is the case remove all access
                     */
                    if (($adminExist && (int) $user->getAdmin() === 0) ||
                         (!$adminExist && (int) $user->getAdmin() > 0)
                       ) {
                        $user->setAdmin(0);
                        $this->getTable("user")->saveUser($user);
                        $this->getTable("administrator")->deleteAdministrator($user->getId());
                        $this->setLayoutMessages("&laquo;".$user->getName()."&raquo; ".$this->translate("ERROR"), 'error');
                        return $this->redirect()->toRoute('admin/default', ['controller' => 'administrator']);
                    }

                    /*
                     * If user is not in admin table and user.admin column is 0
                     */
                    if (!$adminExist && (int) $user->getAdmin() === 0) {
                        $user->setAdmin(1);
                        $this->getTable("user")->saveUser($user);
                        $this->getTable("administrator")->saveAdministrator($administrator);
                        $this->setLayoutMessages("&laquo;".$user->getName()."&raquo; ".$this->translate("SAVE_SUCCESS"), 'success');
                    } else {
                        $this->setLayoutMessages($user->getName().$this->translate("ALREADY_ADMIN"), 'info');
                    }
                } else {
                    $this->setLayoutMessages($this->translate("ERROR"), 'error');
                }
            } else {
                $this->setLayoutMessages($form->getMessages(), 'error');
            }
            return $this->redirect()->toRoute('admin/default', ['controller' => 'administrator']);
        }
    }
}
