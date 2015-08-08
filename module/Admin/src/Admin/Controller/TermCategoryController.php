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

use Admin\Model\TermCategory;
use Admin\Form\TermCategoryForm;

class TermCategoryController extends IndexController
{
    /**
     * @var TermCategoryForm $termCategoryForm
     */
    private $termCategoryForm = null;

    /**
     * @param TermCategoryForm $termCategoryForm
     */
    public function __construct(TermCategoryForm $termCategoryForm = null)
    {
        parent::__construct();

        $this->termCategoryForm = $termCategoryForm;
    }

    /**
     * Initialize any variables before controller actions
     *
     * @param MvcEvent $e
     */
    public function onDispatch(\Zend\Mvc\MvcEvent $e)
    {
        $this->addBreadcrumb(["reference"=>"/admin/termcategory", "name"=>$this->translate("TERM_CATEGORY")]);
        parent::onDispatch($e);
    }

    /**
     * This action shows the list with all term categories
     */
    public function indexAction()
    {
        $this->view->setTemplate("admin/term-category/index");
        $paginator = $this->getTable("termcategory")->fetchList(true);
        $paginator->setCurrentPageNumber((int)$this->getParam("page", 1));
        $paginator->setItemCountPerPage(50);
        $this->view->paginator = $paginator;
        return $this->view;
    }

    /**
     * This action serves for adding a new term category
     */
    protected function addAction()
    {
        $this->view->setTemplate("admin/term-category/add");
        $this->initForm($this->translate("ADD_NEW_TERMCATEGORY"), null);
        $this->addBreadcrumb(["reference"=>"/admin/termcategory/add", "name"=>$this->translate("ADD_NEW_TERMCATEGORY")]);
        return $this->view;
    }

    /**
     * This action presents a modify form for TermCategory object with a given id
     * Upon POST the form is processed and saved
     */
    protected function modifyAction()
    {
        $this->view->setTemplate("admin/term-category/modify");
        $termcategory = $this->getTable("termcategory")->getTermCategory($this->getParam("id", 0))->current();
        $this->view->termcategory = $termcategory;
        $this->addBreadcrumb(["reference"=>"/admin/termcategory/modify/{$termcategory->getId()}", "name"=>$this->translate("MODIFY_TERMCATEGORY")." &laquo".$termcategory->getName()."&raquo;"]);
        $this->initForm($this->translate("MODIFY_TERMCATEGORY"), $termcategory);
        return $this->view;
    }

    /**
     * this action deletes a TermCategory object with a provided id
     */
    protected function deleteAction()
    {
        $this->getTable("termcategory")->deleteTermCategory($this->getParam("id", 0));
        $this->setLayoutMessages($this->translate("DELETE_TERMCATEGORY_SUCCESS"), "success");
        return $this->redirect()->toRoute('admin', ['controller' => 'termcategory']);
    }

    protected function cloneAction()
    {
        $termcategory = $this->getTable("termcategory")->duplicate($this->getParam("id", 0));
        $this->setLayoutMessages("&laquo;".$termcategory->getName()."&raquo; ".$this->translate("CLONE_SUCCESS"), "success");
        return $this->redirect()->toRoute('admin', ['controller' => 'termcategory']);
    }
    /**
     * This is common function used by add and modify actions (to avoid code duplication)
     *
     * @param String $label
     * @param null|TermCategory $termcategory
     */
    private function initForm($label = '', $termcategory = null)
    {
        if (!$termcategory instanceof TermCategory) {
            $termcategory = new TermCategory([], null);
        }

        /**
         * @var TermCategoryForm $form
         */
        $form = $this->termCategoryForm;
        $form->bind($termcategory);
        $form->get("submit")->setValue($label);
        $this->view->form = $form;

        if ($this->getRequest()->isPost()) {
            $form->setInputFilter($form->getInputFilter());
            $form->setData($this->getRequest()->getPost());
            if ($form->isValid()) {
                $this->getTable("termcategory")->saveTermCategory($termcategory);
                $this->setLayoutMessages("&laquo;".$termcategory->getName()."&raquo; ".$this->translate("SAVE_SUCCESS"), "success");
            } else {
                $this->setLayoutMessages($form->getMessages(), "error");
            }
            return $this->redirect()->toRoute('admin', ['controller' => 'termcategory']);
        }
    }
}
