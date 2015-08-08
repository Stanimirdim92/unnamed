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

use Admin\Model\Term;
use Admin\Form\TermForm;

class TermController extends IndexController
{
    /**
     * @var TermForm $termForm
     */
    private $termForm = null;

    /**
     * @param TermForm $termForm
     */
    public function __construct(TermForm $termForm = null)
    {
        parent::__construct();
        $this->termForm = $termForm;
    }

    /**
     * Initialize any variables before controller actions
     *
     * @param MvcEvent $e
     */
    public function onDispatch(\Zend\Mvc\MvcEvent $e)
    {
        $this->addBreadcrumb(["reference"=>"/admin/term", "name"=>$this->translate("TERMS")]);
        parent::onDispatch($e);
    }

    /**
     * This action shows the list with all terms
     */
    public function indexAction()
    {
        $this->view->setTemplate("admin/term/index");
        $paginator = $this->getTable("term")->fetchList(true);
        $paginator->setCurrentPageNumber((int)$this->getParam("page", 1));
        $paginator->setItemCountPerPage(50);
        $this->view->paginator = $paginator;
        return $this->view;
    }

    /**
     * This action serves for adding a new term
     */
    protected function addAction()
    {
        $this->view->setTemplate("admin/term/add");
        $this->initForm($this->translate("ADD_NEW_TERM"), null);
        $this->addBreadcrumb(["reference"=>"/admin/term/add", "name"=>$this->translate("ADD_NEW_TERM")]);
        return $this->view;
    }

    /**
     * This action presents a modify form for Term object with a given id
     * Upon POST the form is processed and saved
     */
    protected function modifyAction()
    {
        $this->view->setTemplate("admin/term/modify");
        $term = $this->getTable("term")->getTerm($this->getParam("id", 0))->current();
        $this->view->term = $term;
        $this->addBreadcrumb(["reference"=>"/admin/term/modify/{$term->getId()}", "name"=>$this->translate("MODIFY_TERM")." &laquo".$term->getName()."&raquo;"]);
        $this->initForm($this->translate("MODIFY_TERM"), $term);
        return $this->view;
    }

    /**
     * this action deletes a Term object with a provided id
     */
    protected function deleteAction()
    {
        $this->getTable("term")->deleteTerm($this->getParam("id", 0), $this->language());
        $this->setLayoutMessages($this->translate("DELETE_TERM_SUCCESS"), "success");
        return $this->redirect()->toRoute('admin', ['controller' => 'term']);
    }

    protected function cloneAction()
    {
        $term = $this->getTable("term")->duplicate($this->getParam("id", 0));
        $this->setLayoutMessages("&laquo;".$term->getName()."&raquo; ".$this->translate("CLONE_SUCCESS"), "success");
        return $this->redirect()->toRoute('admin', ['controller' => 'term']);
    }

    /**
     * This is common function used by add and modify actions (to avoid code duplication)
     *
     * @param String $label
     * @param null|Term $term
     */
    private function initForm($label = '', Term $term = null)
    {
        if (!$term instanceof Term) {
            $term = new Term([], null);
        }

        $termcategory = $this->getTable("termcategory")->fetchList();
        $valueOptions = [];

        foreach ($termcategory as $item) {
            $valueOptions[$item->getId()] = $item->getName();
        }

        /**
         * @var TermForm $form
         */
        $form = $this->termForm;
        $form->bind($term);
        $form->get("termcategory")->setValueOptions($valueOptions);
        $form->get("submit")->setValue($label);
        $this->view->form = $form;

        if ($this->getRequest()->isPost()) {
            $form->setInputFilter($form->getInputFilter());
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $this->getTable("term")->saveTerm($term);
                $this->setLayoutMessages("&laquo;".$term->getName()."&raquo; ".$this->translate("SAVE_SUCCESS"), "success");
            } else {
                $this->setLayoutMessages($form->getMessages(), "error");
            }
            return $this->redirect()->toRoute('admin', ['controller' => 'term']);
        }
    }
}
