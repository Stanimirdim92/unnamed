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
 * @version    0.0.13
 * @link       TBA
 */

namespace Admin\Controller;

use Admin\Model\Language;
use Admin\Form\LanguageForm;
use Admin\Exception\RunTimeException;
use Zend\Stdlib\Parameters;

final class LanguageController extends IndexController
{
    /**
     * @var LanguageForm $languageForm
     */
    private $languageForm = null;

    /**
     * @param LanguageForm $languageForm
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
        parent::onDispatch($e);
        $this->addBreadcrumb(["reference"=>"/admin/language", "name"=>$this->translate("LANGUAGE")]);
    }

    /**
     * This action shows the list of all (or filtered) Language objects
     *
     * @return ViewModel
     */
    public function indexAction()
    {
        $this->getView()->setTemplate("admin/language/index");
        $paginator = $this->getTable("language")->fetchList(true);
        $paginator->setCurrentPageNumber((int)$this->getParam("page", 1));
        $paginator->setItemCountPerPage(20);
        $this->getView()->paginator = $paginator;
        return $this->getView();
    }

    /**
     * This action serves for adding a new object of type Language
     *
     * @return ViewModel
     */
    protected function addAction()
    {
        $this->getView()->setTemplate("admin/language/add");
        $this->initForm($this->translate("ADD_LANGUAGE"), null);
        $this->addBreadcrumb(["reference"=>"/admin/language/add", "name"=>$this->translate("ADD_LANGUAGE")]);
        return $this->getView();
    }

    /**
     * This action presents a modify form for Language object with a given id
     * Upon POST the form is processed and saved
     *
     * @return ViewModel
     */
    protected function modifyAction()
    {
        $this->getView()->setTemplate("admin/language/modify");
        $language = $this->getTable("language")->getLanguage((int)$this->getParam("id", 0));
        $this->getView()->language = $language;
        $this->addBreadcrumb(["reference"=>"/admin/language/modify/{$language->getId()}", "name"=>$this->translate("MODIFY_LANGUAGE")." &laquo;".$language->getName()."&raquo;"]);
        $this->initForm($this->translate("MODIFY_LANGUAGE"), $language);
        return $this->getView();
    }

    /**
     * this action deletes a language object with a provided id
     */
    protected function deleteAction()
    {
        $this->getTable("language")->deleteLanguage((int)$this->getParam('id', 0));
        $this->setLayoutMessages($this->translate("DELETE_LANGUAGE_SUCCESS"), "success");
        return $this->redirect()->toRoute('admin/default', ['controller' => 'language']);
    }

    /**
     * this action shows language details from the provided id
     *
     * @return ViewModel
     */
    protected function detailAction()
    {
        $this->getView()->setTemplate("admin/language/detail");
        $lang = $this->getTable("Language")->getLanguage((int)$this->getParam('id', 0));
        $this->getView()->lang = $lang;
        $this->addBreadcrumb(["reference"=>"/admin/language/detail/{$lang->getId()}", "name"=>"&laquo;". $lang->getName()."&raquo; ".$this->translate("DETAILS")]);
        return $this->getView();
    }

    /**
     * This method will get the translation file based on the $_SESSION["languageName"] variable.
     * If no such file is found, the system will try to return the backup file.
     * If the backup file is not found for any reason, an exception will be thrown
     *
     * @throws RunTimeException if no file is found
     *
     * @return  ViewModel
     */
    protected function translationsAction()
    {
        $this->getView()->setTemplate("admin/language/translations");

        $arr = "module/Application/languages/phpArray/".$this->language("languageName").".php";

        if (!is_file($arr)) {
            $arr = "module/Application/languages/phpArray/en.php";
        }

        if (!is_file($arr)) {
            $arr = "data/translations/en_backup.php";
        }

        if (!is_file($arr)) {
            throw new RunTimeException($this->translate('NO_TRANSLATION_FILE'));
        }

        $this->getView()->translationsArray = include $arr;

        $request = $this->getRequest();
        if ($request->isPost()) {
            if ($request->getPost() instanceof Parameters) {
                $filename = "module/Application/languages/phpArray/".$this->language("languageName").".php";
                $arr2 = $request->getPost()->toArray();
                unset($arr2["submit"]); // remove the submit button
                file_put_contents($filename, '<?php return ' . var_export($arr2, true).';');
                $this->setLayoutMessages($this->translate("TRANSLATIONS_SAVE_SUCCESS"), "success");
            }
            return $this->redirect()->toRoute('admin/default', ['controller' => 'language']);
        }
        return $this->getView();
    }

    /**
     * This is common function used by add and modify actions (to avoid code duplication)
     *
     * @param String $label button title
     * @param Language $language object
     */
    private function initForm($label = '', Language $language = null)
    {
        if (!$language instanceof Language) {
            $language = new Language([]);
        }

        /**
         * @var $form LanguageForm
         */
        $form = $this->languageForm;
        $form->get("submit")->setValue($label);
        $form->bind($language);
        $this->getView()->form = $form;
        if ($this->getRequest()->isPost()) {
            $form->setInputFilter($form->getInputFilter());
            $form->setData($this->getRequest()->getPost());
            if ($form->isValid()) {
                $this->getTable("language")->saveLanguage($language);
                $this->setLayoutMessages($this->translate("LANGUAGE")." &laquo;".$language->getName()."&raquo; ".$this->translate("SAVE_SUCCESS"), 'success');
            } else {
                $this->setLayoutMessages($form->getMessages(), 'error');
            }
            return $this->redirect()->toRoute('admin/default', ['controller' => 'language']);
        }
    }
}
