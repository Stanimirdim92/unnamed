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
 *mits
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
 * @version    0.0.7
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
     * Initialize any variables before controller actions
     *
     * @param MvcEvent $e
     */
    public function onDispatch(\Zend\Mvc\MvcEvent $e)
    {
        parent::onDispatch($e);
        $this->addBreadcrumb(["reference"=>"/admin/administrator", "name"=>$this->translate("ADMINISTRATORS")]);
    }

    /**
     * This action shows the list of all Administrators
     */
    public function indexAction()
    {
        $this->view->setTemplate("admin/administrator/index");
        $paginator = $this->getTable("administrator")->fetchJoin(true, "user", ["user"], ["name"], "administrator.user=user.id", "left");
        $paginator->setCurrentPageNumber((int)$this->getParam("page", 1));
        $paginator->setItemCountPerPage(20);
        $this->view->paginator = $paginator;
        return $this->view;
    }

    /**
     * This action serves for adding a new users as administrators
     */
    protected function addAction()
    {
        $this->view->setTemplate("admin/administrator/add");
        $this->initForm($this->translate("ADD_ADMINISTRATOR"), null);
        $this->addBreadcrumb(["reference"=>"/admin/administrator/add", "name"=>$this->translate("ADD_ADMINISTRATOR")]);
        return $this->view;
    }

    /**
     * This action presents a modify form for Administrator object with a given id
     * Upon POST the form is processed and saved
     */
    protected function modifyAction()
    {
        $this->view->setTemplate("admin/administrator/modify");
        $administrator = $this->getTable("administrator")->getAdministrator($this->getParam("id", 0))->current();
        $this->view->administrator = $administrator;
        $this->addBreadcrumb(["reference"=>"/admin/administrator/modify/{$administrator->getUser()}", "name"=>$this->translate("MODIY_ADMINISTRATOR")]);
        $this->initForm($this->translate("MODIY_ADMINISTRATOR"), $administrator);
        return $this->view;
    }

    /**
     * this action deletes a administrator
     */
    protected function deleteAction()
    {
        $id = $this->getParam('id', 0);
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
     * to search for existing users and add them as administrators
     *
     * @return JsonModel
     */
    protected function searchAction()
    {
        $search = (string) $this->getParam('ajaxsearch');
        $this->view->setTerminal(true);
        if (isset($search) && $this->getRequest()->isXmlHttpRequest()) {
            $where = "`name` LIKE '%{$search}%' OR `surname` LIKE '%{$search}%' OR `email` LIKE '%{$search}%'";
            $results = $this->getTable("user")->fetchList(false, ["id", "name", "surname", "email"], $where);

            $json = [];
            $success = false;
            if ($results) {
                $results = $results->getDataSource();
                foreach ($results as $result) {
                    $json[] = Json::encode($result);
                }
                $success = true;
            }

            return new JsonModel([
                'ajaxsearch' => $json,
                'statusType' => $success,
            ]);
        }
    }

    /**
     * This is common function used by add and modify actions (to avoid code duplication)
     *
     * @param String $label
     * @param null|Administrator $administrator
     */
    private function initForm($label = '', Administrator $administrator = null)
    {
        if (!$administrator instanceof Administrator) {
            $administrator = new Administrator([], null);
        }

        /**
         * @var $form AdministratorForm
         */
        $form = $this->administratorForm;
        $form->get("submit")->setValue($label);
        $form->bind($administrator);
        $this->view->form = $form;

        if ($this->getRequest()->isPost()) {
            $form->setInputFilter($form->getInputFilter());
            $form->setData($this->getRequest()->getPost());
            if ($form->isValid()) {
                $formData = $form->getData();
                $user = $this->getTable("user")->getUser($formData->user)->current();
                // valid user id
                if (count($user) === 1) {
                    // should return false|null|0 etc.
                    $adminExist = $this->getTable("administrator")->getAdministrator($user->getId())->current();
                    /**
                     * See if user is already admin
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
