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
 * @version    0.0.5
 * @link       TBA
 */

namespace Admin\Controller;

use Admin\Model\Language;
use Admin\Form\LanguageForm;

class LanguageController extends IndexController
{
    /**
     * @var Admin\Form\LanguageForm $languageForm
     */
    private $languageForm = null;

    /**
     * @param Admin\Form\LanguageForm $languageForm
     */
    public function __construct(LanguageForm $languageForm = null)
    {
        parent::__construct();

        $this->languageForm = $languageForm;
    }

    /**
     * Initialize any variables before controller actions
     *
     * @param MvcEvent $e
     */
    public function onDispatch(\Zend\Mvc\MvcEvent $e)
    {
        $this->addBreadcrumb(["reference"=>"/admin/language", "name"=>$this->translate("LANGUAGE")]);
        parent::onDispatch($e);
    }

    /**
     * This action shows the list of all (or filtered) Language objects
     */
    public function indexAction()
    {
        $this->view->setTemplate("admin/language/index");
        $paginator = $this->getTable("language")->fetchList(true);
        $paginator->setCurrentPageNumber((int)$this->getParam("page", 1));
        $paginator->setItemCountPerPage(20);
        $this->view->paginator = $paginator;
        return $this->view;
    }

    /**
     * This action serves for adding a new object of type Language
     */
    protected function addAction()
    {
        $this->view->setTemplate("admin/language/add");
        $this->initForm($this->translate("ADD_LANGUAGE"), null);
        $this->addBreadcrumb(["reference"=>"/admin/language/add", "name"=>$this->translate("ADD_LANGUAGE")]);
        return $this->view;
    }

    /**
     * This action presents a modify form for Language object with a given id
     * Upon POST the form is processed and saved
     */
    protected function modifyAction()
    {
        $this->view->setTemplate("admin/language/modify");
        $language = $this->getTable("language")->getLanguage($this->getParam("id", 0));
        $this->view->language = $language;
        $this->addBreadcrumb(["reference"=>"/admin/language/modify/{$language->getId()}", "name"=>$this->translate("MODIFY_LANGUAGE")." &laquo;".$language->getName()."&raquo;"]);
        $this->initForm($this->translate("MODIFY_LANGUAGE"), $language);
        return $this->view;
    }

    /**
     * this action deletes a language object with a provided id
     */
    protected function deleteAction()
    {
        $this->getTable("language")->deleteLanguage($this->getParam('id', 0));
        $this->setLayoutMessages($this->translate("DELETE_LANGUAGE_SUCCESS"), "success");
        return $this->redirect()->toRoute('admin', ['controller' => 'language']);
    }

    /**
     * this action shows language details from the provided id
     */
    protected function detailAction()
    {
        $this->view->setTemplate("admin/language/detail");
        $lang = $this->getTable("Language")->getLanguage($this->getParam('id', 0));
        $this->view->lang = $lang;
        $this->addBreadcrumb(["reference"=>"/admin/language/detail/{$lang->getId()}", "name"=>"&laquo;". $lang->getName()."&raquo; ".$this->translate("DETAILS")]);
        return $this->view;
    }

    /**
     * This is common function used by add and modify actions (to avoid code duplication)
     *
     * @param String $label button title
     * @param null|Language $language object
     */
    private function initForm($label = '', Language $language = null)
    {
        if (!$language instanceof Language) {
            $language = new Language([], null);
        }

        /**
         * @var $form Admin\Form\LanguageForm
         */
        $form = $this->languageForm;
        $form->get("submit")->setValue($label);
        $form->bind($language);
        $this->view->form = $form;
        if ($this->getRequest()->isPost()) {
            $form->setInputFilter($form->getInputFilter());
            $form->setData($this->getRequest()->getPost());
            if ($form->isValid()) {
                $this->getTable("language")->saveLanguage($language);
                $this->setLayoutMessages($this->translate("LANGUAGE")." &laquo;".$language->getName()."&raquo; ".$this->translate("SAVE_SUCCESS"), 'success');
                return $this->redirect()->toRoute('admin', ['controller' => 'language']);
            } else {
                $this->setLayoutMessages($form->getMessages(), 'error');
                return $this->redirect()->toRoute('admin', ['controller' => 'language']);
            }
        }
    }
}
