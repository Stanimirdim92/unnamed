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
 * @category   Admin\Administrator
 * @package    ZendPress
 * @author     Stanimir Dimitrov <stanimirdim92@gmail.com>
 * @copyright  2015 Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 * @version    0.03
 * @link       TBA
 */

namespace Admin\Controller;

use Admin\Model\Administrator;

class AdministratorController extends \Admin\Controller\IndexController
{
    /**
     * Controller name to which will redirect
     */
    const CONTROLLER_NAME = "administrator";

    /**
     * Route name to which will redirect
     */
    const ADMIN_ROUTE = "admin";

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
        $paginator = $this->getTable("administrator")->fetchList(true, array(), array(), null, null, "id DESC");
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
        $this->showForm("Add administrator", null);
        $this->addBreadcrumb(array("reference"=>"/admin/administrator/add", "name"=>"Add new administrator"));
        return $this->view;
    }

    /**
     * This action presents a modify form for Administrator object with a given id
     * Upon POST the form is processed and saved
     */
    public function modifyAction()
    {
        $administrator = $this->getTable("administrator")->getAdministrator($this->getParam("id", 0));
        $this->view->administrator = $administrator;
        $this->addBreadcrumb(array("reference"=>"/admin/administrator/modify/id/{$administrator->user}", "name"=>"Modify administrator"));
        $this->showForm("Modify administrator", $administrator);
        return $this->view;
    }

    /**
     * this action deletes a administrator object with a provided id
     */
    public function deleteAction()
    {
        $user = $this->getTable("user")->getUser($this->getParam('id', 0));
        $user->setAdmin(0);
        $this->getTable("user")->saveUser($user);
        $this->getTable("administrator")->deleteAdministrator($this->getParam('id', 0));
        $this->cache->success = "Administrator was successfully deleted";
        return $this->redirect()->toRoute(self::ADMIN_ROUTE, array('controller' => self::CONTROLLER_NAME));
    }

   /**
     * This is common function used by add and modify actions (to avoid code duplication)
     *
     * @param String $label
     * @param null|Administrator $administrator
     */
    private function showForm($label='Add administrator', Administrator $administrator = null)
    {
        if($administrator==null) $administrator = new Administrator(array(), null);

        $form = new \Admin\Form\AdministratorForm($administrator);
        $form->get("submit")->setValue($label);
        $this->view->form = $form;
        if($this->getRequest()->isPost())
        {
            $form->setInputFilter($administrator->getInputFilter());
            $form->setData($this->getRequest()->getPost());
            if($form->isValid())
            {
                $formData = $form->getData();

                $user = $this->getTable("user")->getUser($formData['user']);
                // valid user id
                if (count($user) == 1)
                {
                    $adminExist = $this->getTable("administrator")->getAdministrator($user->id);
                    if (count($adminExist) != 0)
                    {
                        $this->cache->error = $user->toString()." is already administrator";
                        $this->view->setTerminal(true);
                        return $this->redirect()->toRoute(self::ADMIN_ROUTE, array('controller' => self::CONTROLLER_NAME));
                    }
                    else
                    {
                        $user->setAdmin(1);
                        $administrator->exchangeArray($formData);
                        $this->getTable("user")->saveUser($user);
                        $this->getTable("administrator")->saveAdministrator($administrator);
                        $this->cache->success = "Administrator was successfully saved";
                        $this->view->setTerminal(true);
                        return $this->redirect()->toRoute(self::ADMIN_ROUTE, array('controller' => self::CONTROLLER_NAME));
                    }
                }
                else
                {
                    $this->setErrorNoParam(IndexController::NO_ID);
                    return $this->redirect()->toRoute(self::ADMIN_ROUTE, array('controller' => self::CONTROLLER_NAME));
                }
            }
            else
            {
                $error = array();
                foreach($form->getMessages() as $msg)
                {
                    foreach ($msg as $key => $value)
                    {
                        $error = $value;
                    }
                }
                $this->setErrorNoParam($error);
                return $this->redirect()->toRoute(self::ADMIN_ROUTE, array('controller' => self::CONTROLLER_NAME));
            }
        }
    }
}
