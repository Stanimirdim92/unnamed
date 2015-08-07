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

use Admin\Model\TermTranslation;
use Admin\Model\Term;
use Admin\Form\TermTranslationForm;

class TermTranslationController extends IndexController
{
    /**
     * Initialize any variables before controller actions
     *
     * @param MvcEvent $e
     */
    public function onDispatch(\Zend\Mvc\MvcEvent $e)
    {
        $this->addBreadcrumb(["reference" => "/admin/termtranslation", "name" => $this->translate("TERM_TRANSLATIONS")]);
        parent::onDispatch($e);
    }

    public function indexAction()
    {
        $this->view->setTemplate("admin/term-translation/index");
        $this->view->termCategories = $this->getTable("termcategory")->fetchList();
        return $this->view;
    }

    /**
     * This action serves for adding a new object of type Term
     */
    protected function addAction()
    {
        $this->view->setTemplate("admin/term-translation/add");
        $this->initForm($this->translate("ADD_NEW_TERM_TRANSLATION"), null);
        $this->addBreadcrumb(["reference" => "/admin/termtranslation/add", "name" => $this->translate("ADD_NEW_TERM_TRANSLATION")]);
        return $this->view;
    }

    /**
     * This action presents a modify form for TermTranslation object with a given id
     * Upon POST the form is processed and saved
     */
    protected function modifyAction()
    {
        $this->view->setTemplate("admin/term-translation/modify");
        $termcategory = $this->getTable("termcategory")->getTermCategory((int) $this->getParam('id', 0))->current();
        $this->view->termcategory = $termcategory;
        $this->addBreadcrumb(["reference" => "/admin/termtranslation/modify/{$termcategory->getId()}", "name" => $this->translate("MODIFY_TERM_TRANSLATION")." &laquo".$termcategory->getName()."&raquo;"]);
        $this->initForm($this->translate("MODIFY"), $termcategory);
        return $this->view;
    }

    /**
     * This is common function used by add and modify actions (to avoid code duplication)
     *
     * @param String $label
     * @param null|TermTranslation $termtranslation
     */
    private function initForm($label = '', $termtranslation = null)
    {
        $termTranslations = [];
        $tt = $this->getTable("termtranslation")->fetchJoin(false, "term", ["translation", "term", "id"], ["termcategory", "name"], "term.id=termtranslation.term", "inner", ["termtranslation.language" => $this->language(), "term.termcategory" => (int) $this->getParam("id", 0)]);
        foreach ($tt->getDataSource() as $t) {
            $termTranslations[$t["term"]] = $t;
        }
        $form = new TermTranslationForm();
        $form->bind($termTranslations);
        $form->get("submit")->setValue($label);

        $this->view->form = $form;
        if ($this->getRequest()->isPost()) {
            // $form->setInputFilter($termtranslation->getInputFilter());
            $form->setData($this->getRequest()->getPost());
            if ($form->isValid()) {
                echo \Zend\Debug\Debug::dump($form->getData(), null, false);
                exit;
                // $formData = $this->getRequest()->getPost()->toArray();
                // $keys = array_keys($formData);
                // for ($i = 0; $i < sizeof($keys) - 2; $i++) {
                //     if (strstr($keys[$i], "translation")!==false) {
                //         $termId = substr($keys[$i], 11);
                //         $existing = $this->getTable("termtranslation")->fetchList(false, "term='{$termId}' AND language='{$this->session->language}'");
                //         if (sizeof($existing) != 0) {
                //             $model = $existing->current();
                //         } else {
                //             $model = new TermTranslation();
                //         }
                //         $model->setLanguage($this->session->language);
                //         $model->setTerm($termId);
                //         $model->setTranslation($formData[$keys[$i]]);
                //         $this->getTable("termtranslation")->saveTermTranslation($model);
                //     }
                // }

                // $this->translation->success = "Term translation &laquo;".$termtranslation->toString()."&raquo; was successfully saved";
                // $this->view->setTerminal(true);
                // return $this->redirect()->toRoute('admin', ['controller' => 'termtranslation']);
            } else {
                $this->setLayoutMessages($form->getMessages(), "error");
                return $this->redirect()->toRoute('admin', ['controller' => 'termtranslation']);
            }
        }
    }

    /** this action deletes a TermTranslation object with a provided id */
    protected function deleteAction()
    {
        $id = (int) $this->getParam('id', 0);
        if (!$id) {
            $this->setErrorNoParam($this->NO_ID);
            return $this->redirect()->toRoute('admin', ['controller' => 'termtranslation']);
        }
        try {
            $this->getTable("termtranslation")->deleteTermTranslation($id);
        } catch (\Exception $ex) {
            $this->setErrorNoParam("Term translation not found");
            return $this->redirect()->toRoute('admin', ['controller' => 'termtranslation']);
        }
        $this->translation->success = "Term translation was successfully deleted";
        $this->redirect()->toRoute('admin', ['controller' => 'termtranslation']);
    }

    // this is a new import that works in a much straightforward way
    // simply read the term definitions and find that term
    // check if a translation exists and if not add one
    // if duplicate terms are found remove all but one and notify the user
    protected function importAction()
    {
        require_once($_SERVER["DOCUMENT_ROOT"]."/vendor/CodePlex/PHPExcel.php");
        require_once($_SERVER["DOCUMENT_ROOT"]."/vendor/CodePlex/PHPExcel/PHPExcel_IOFactory.php");
        $error = "";
        // first of all pick a random category and use its id
        // in case the excel for some weird reason doesn't have categories
        // if no categories exist create one
        // cache the existing categories to check later
        $categories = $this->getTable("termcategory")->fetchList(false, null, "id ASC");
        $existingCategories = [];
        if (sizeof($categories) == 0) {
            $category = new TermCategory();
            $category->name = "all terms";
            $category->save();
            $existingCategories[] = $category->id;
        } else {
            foreach ($categories as $c) {
                $existingCategories[] = $c->id;
            }
        }
        if ($this->getRequest()->isPost()) {
            if ($FILES["excel"]["size"] > 0) {
                $fileName = $FILES["excel"]["tmp_name"];
                $objPHPExcel = \PHPExcel_IOFactory::load($fileName);
                $numberOfSheets = $objPHPExcel->getSheetCount();
                for ($i = 0; $i < $numberOfSheets; $i ++) {
                    $sheet = $objPHPExcel->getSheet($i);
                    $language = $sheet->getCellByColumnAndRow(1, 1)->getCalculatedValue();
                    if (is_numeric($language) and $language > 0) {
                        $row = 4;
                        $termLabel = strtoupper($sheet->getCellByColumnAndRow(2, $row)->getCalculatedValue());
                        while ($termLabel != "") {
                            // find the term in the DB
                            $term = null;
                            $terms = $this->getTable("term")->fetchList(false, "name='{$termLabel}'");
                            if (sizeof($terms) == 0) {
                                // add the term
                                $categoryId = $sheet->getCellByColumnAndRow(0, $row)->getCalculatedValue();
                                // make sure category exists if not simply pick the first existing category
                                if (!in_array($categoryId, $existingCategories)) {
                                    $categoryId = $existingCategories[0];
                                }
                                $term = new Term();
                                $term->setName($termLabel);
                                $term->setTermCategory($categoryId);
                                $term = $this->getTable("term")->saveTerm($term);
                            } elseif (sizeof($terms) == 1) {
                                $term = $terms->current();
                            } else {
                                $error = $this->session->TERM . $terms->current()->name . "term translation contains duplicates <br>" . $error;
                                $term = $terms->current();
                                $terms->next();
                                foreach ($terms as $t) {
                                    $this->getTable("term")->deleteTerm($t->id);
                                }
                            }
                            // so far we are sure the term exists now we add the translation
                            // first of all try to fetch the translation
                            $translations = $this->getTable("termtranslation")->fetchList(false, "term='{$term->id}' AND language='{$language}'");
                            if (sizeof($translations) > 0) {
                                $translation = $translations->current();
                            } else {
                                $translation = new TermTranslation();
                            }
                            $translation->term = $term->id;
                            $translation->language = $language;
                            $text = trim($sheet->getCellByColumnAndRow(3, $row)->getCalculatedValue());
                            $translation->translation = $text;
                            $this->getTable("termtranslation")->saveTermTranslation($translation);
                            $row ++;
                            $termLabel = strtoupper($sheet->getCellByColumnAndRow(2, $row)->getCalculatedValue());
                        }
                    }
                }
            }
        }
        if ($error != "") {
            $this->setErrorNoParam($error);
        } else {
            $this->setErrorNoParam("Export was successfull");
        }
        $this->view->setTerminal(true);
        return $this->redirect()->toRoute('admin', ['controller' => 'termtranslation']);
    }

    protected function exportAction()
    {
        require_once($_SERVER["DOCUMENT_ROOT"]."/vendor/CodePlex/PHPExcel.php");
        // create excel file
        $id = rand(10000, 99999999);
        $file = md5($id) . ".xlsx";
        $filename = $_SERVER['DOCUMENT_ROOT'] . "/public/userfiles/exports/" . $file;
        $objPHPExcel = new \PHPExcel();
        $objPHPExcel->getProperties()
            ->setCreator("Xtreme-Jumps Excel Export Plugin")
            ->setTitle("Office 2007 XLS Export Document")
            ->setSubject("Office 2007 XLS Export Document")
            ->setDescription("Excel Autoexport");
        $languages = $this->getTable("language")->fetchList();
        $i = 0;
        foreach ($languages as $l) {
            if ($i > 0) {
                $objPHPExcel->createSheet();
                $objPHPExcel->setActiveSheetIndex($i);
            }
            $i ++;
            $sheet = $objPHPExcel->getActiveSheet();
            $sheet->setTitle($l->name . " translations");
            $sheet->setCellValueExplicitByColumnAndRow(0, 1, "id");
            $sheet->setCellValueExplicitByColumnAndRow(1, 1, $l->id, \PHPExcel_Cell_DataType::TYPE_NUMERIC);
            $sheet->setCellValueExplicitByColumnAndRow(0, 2, "language");
            $sheet->setCellValueExplicitByColumnAndRow(1, 2, $l->name);
            $categories = $this->getTable("termcategory")->fetchList(false, null, "name ASC");
            $sheet->setCellValueExplicitByColumnAndRow(0, 3, "id (internal)");
            $sheet->setCellValueExplicitByColumnAndRow(1, 3, "category name");
            $sheet->setCellValueExplicitByColumnAndRow(2, 3, "term definition (do not change)");
            $sheet->setCellValueExplicitByColumnAndRow(3, 3, "term translation");
            $row = 4;
            foreach ($categories as $c) {
                $terms = $this->getTable("term")->fetchList(false, "termcategory='{$c->id}'", "name ASC");
                foreach ($terms as $t) {
                    $sheet->setCellValueExplicitByColumnAndRow(0, $row, $c->id, \PHPExcel_Cell_DataType::TYPE_NUMERIC);
                    $sheet->setCellValueExplicitByColumnAndRow(1, $row, $c->name);
                    // $sheet->setCellValueExplicitByColumnAndRow(2, $row, $t->getId());
                    $sheet->setCellValueExplicitByColumnAndRow(2, $row, $t->name);
                    $trs = $this->getTable("termtranslation")->fetchList(false, "term='{$t->id}' AND language='{$l->id}'");
                    if (sizeof($trs) > 0) {
                        $sheet->setCellValueExplicitByColumnAndRow(3, $row, $trs->current()->translation);
                    }
                    $row ++;
                }
            }
        }

        $objWriter = new \PHPExcel_Writer_Excel2007($objPHPExcel);
        $objWriter->save($filename);

        // and redirect to it
        $this->redirect()->toUrl("/userfiles/exports/{$file}");
    }
}
