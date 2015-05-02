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
 * @category
 * @package
 * @author     Stanimir Dimitrov <stanimirdim92@gmail.com>
 * @copyright  2015 Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 * @version    0.03
 * @link       TBA
 */
namespace Admin\Controller;

use Admin\Model\Language;

class LanguageController extends \Admin\Controller\IndexController
{
    /**
     * Controller name to which will redirect
     */
    const CONTROLLER_NAME = "language";

    /**
     * Route name to which will redirect
     */
    const ADMIN_ROUTE = "admin";

    /**
     * Initialize any variables before controller actions
     *
     * @param MvcEvent $e
     */

    /**
     * Initialize any variables before controller actions
     *
     * @param MvcEvent $e
     */
    public function onDispatch(\Zend\Mvc\MvcEvent $e)
    {
        $this->addBreadcrumb(array("reference"=>"/admin/language", "name"=>"Languages"));
        parent::onDispatch($e);
    }

    /**
     * This action shows the list of all (or filtered) Language objects
     */
    public function indexAction()
    {
        $paginator = $this->getTable("language")->fetchList(true, array(), array(), null, null, "name ASC");
        $paginator->setCurrentPageNumber((int)$this->params("page",1));
        $paginator->setItemCountPerPage(50);
        $this->view->paginator = $paginator;
        return $this->view;
    }

    /**
     * This action serves for adding a new object of type Language
     */
    public function addAction()
    {
        $this->showForm("Add language", null);
        $this->addBreadcrumb(array("reference"=>"/admin/language/add", "name"=>"Add new language"));
        return $this->view;
    }

    /**
     * This action presents a modify form for Language object with a given id
     * Upon POST the form is processed and saved
     */
    public function modifyAction()
    {
        $language = $this->getTable("language")->getLanguage($this->getParam("id", 0));
        $this->view->language = $language;
        $this->addBreadcrumb(array("reference"=>"/admin/language/modify/id/{$language->id}", "name"=>"Modify language &laquo;".$language->toString()."&raquo;"));
        $this->showForm("Modify language", $language);
        return $this->view;
    }

    /**
     * this action deletes a language object with a provided id
     */
    public function deleteAction()
    {
        $this->getTable("language")->deleteLanguage($this->getParam('id', 0));
        $this->cache->success = "Language was successfully deleted";
        return $this->redirect()->toRoute(self::ADMIN_ROUTE, array('controller' => self::CONTROLLER_NAME));
    }

    /**
     * this action shows language details from the provided id
     */
    public function detailAction()
    {
        $lang = $this->getTable("Language")->getLanguage($this->getParam('id', 0));
        $this->view->lang = $lang;
        $this->addBreadcrumb(array("reference"=>"/admin/language/detail/id/{$lang->id}", "name"=>"language &laquo;". $lang->toString()."&raquo; details"));
        return $this->view;
    }

    /**
     * This is common function used by add and modify actions (to avoid code duplication)
     *
     * @param String $label button title
     * @param null|Language $language object
     */
    public function showForm($label = '', Language $language = null)
    {
        if ($language == null) {
            $language = new Language(array(), null);
        }

        $language->setServiceManager(null);
        $form = new \Admin\Form\LanguageForm($language);
        $form->get("submit")->setValue($label);
        $this->view->form = $form;
        if ($this->getRequest()->isPost()) {
            $form->setInputFilter($language->getInputFilter());
            $form->setData($this->getRequest()->getPost());
            if ($form->isValid()) {
                $language->exchangeArray($form->getData());
                $this->getTable("language")->saveLanguage($language);
                $this->cache->success = $this->translation->LANGUAGE."&nbsp;&laquo;".$language->toString()."&raquo; ".$this->translation->SAVE_SUCCESS;
                $this->view->setTerminal(true);
                return $this->redirect()->toRoute(self::ADMIN_ROUTE, array('controller' => self::CONTROLLER_NAME));
            } else {
                $error = array();
                foreach ($form->getMessages() as $msg) {
                    foreach ($msg as $key => $value) {
                        $error[] = $value;
                    }
                }
                $this->setErrorNoParam($error);
                return $this->redirect()->toRoute(self::ADMIN_ROUTE, array('controller' => self::CONTROLLER_NAME));
            }
        }
    }
}
