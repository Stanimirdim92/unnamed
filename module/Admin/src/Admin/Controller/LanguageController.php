<?php

/**
 * @copyright  2015 (c) Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 *
 * @version    0.0.21
 *
 * @link       TBA
 */

namespace Admin\Controller;

use Admin\Entity\Language;
use Admin\Form\LanguageForm;
use Admin\Exception\RunTimeException;
use Zend\Stdlib\Parameters;
use Zend\Mvc\MvcEvent;

final class LanguageController extends BaseController
{
    /**
     * @var LanguageForm
     */
    private $languageForm;

    /**
     * @var \Admin\Model\LanguageTable
     */
    private $languageTable;

    /**
     * @param LanguageForm $languageForm
     */
    public function __construct(LanguageForm $languageForm)
    {
        parent::__construct();

        $this->languageForm = $languageForm;
    }

    /**
     * @param MvcEvent $event
     *
     * @return mixed|void
     */
    public function onDispatch(MvcEvent $event)
    {
        $this->addBreadcrumb(["reference"=>"/admin/language", "name"=>$this->translate("LANGUAGE")]);
        $this->languageTable = $this->getTable("Admin\\Model\\LanguageTable");

        parent::onDispatch($event);
    }

    /**
     * This action shows the list of all (or filtered) Language objects.
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function indexAction()
    {
        $this->getView()->setTemplate("admin/language/index");
        $table = $this->languageTable;

        $query = $table->queryBuilder()
                   ->select(["l"])
                   ->from('Admin\Entity\Language', 'l');

        $paginator = $table->preparePagination($query, false);
        $paginator->setCurrentPageNumber((int)$this->getParam("page", 1));
        $paginator->setItemCountPerPage($this->systemSettings('posts', 'language'));
        $this->getView()->paginator = $paginator;

        return $this->getView();
    }

    /**
     * This action serves for adding a new object of type Language.
     *
     * @return \Zend\View\Model\ViewModel
     */
    protected function addAction()
    {
        $this->getView()->setTemplate("admin/language/add");
        $this->initForm();
        $this->addBreadcrumb(["reference"=>"/admin/language/add", "name"=>$this->translate("ADD_LANGUAGE")]);

        return $this->getView();
    }

    /**
     * This action presents a edit form for Language object with a given id.
     * Upon POST the form is processed and saved.
     *
     * @return \Zend\View\Model\ViewModel
     */
    protected function editAction()
    {
        $this->getView()->setTemplate("admin/language/edit");
        $language = $this->languageTable->getLanguage((int)$this->getParam("id", 0));
        $this->getView()->language = $language;
        $this->addBreadcrumb(["reference"=>"/admin/language/edit/{$language->getId()}", "name"=>$this->translate("EDIT_LANGUAGE")." &laquo;".$language->getName()."&raquo;"]);
        $this->initForm($language);

        return $this->getView();
    }

    /**
     * this action deletes a language object with a provided id.
     */
    protected function deleteAction()
    {
        $this->languageTable->deleteLanguage((int)$this->getParam('id', 0));
        $this->setLayoutMessages($this->translate("DELETE_LANGUAGE_SUCCESS"), "success");
    }

    /**
     * this action shows language details from the provided id.
     *
     * @return \Zend\View\Model\ViewModel
     */
    protected function detailAction()
    {
        $this->getView()->setTemplate("admin/language/detail");
        $lang = $this->languageTable->getLanguage((int)$this->getParam('id', 0));
        $this->getView()->lang = $lang;
        $this->addBreadcrumb(["reference"=>"/admin/language/detail/{$lang->getId()}", "name"=>"&laquo;". $lang->getName()."&raquo; ".$this->translate("DETAILS")]);

        return $this->getView();
    }

    /**
     * This method will get the translation file based on the $_SESSION["languageName"] variable.
     * If no such file is found, the system will try to return the backup file.
     * If the backup file is not found for any reason, an exception will be thrown.
     *
     * @throws RunTimeException if no file is found
     *
     * @return \Zend\View\Model\ViewModel
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
        if ($request->isPost() && $request->getPost() instanceof Parameters) {
            $filename = "module/Application/languages/phpArray/".$this->language("languageName").".php";
            $arr2 = $request->getPost()->toArray();
            unset($arr2["submit"]); // remove submit button
            file_put_contents($filename, '<?php return ' . var_export($arr2, true).';');
            $this->setLayoutMessages($this->translate("TRANSLATIONS_SAVE_SUCCESS"), "success");
        }

        return $this->getView();
    }

    /**
     * This is common function used by add and edit actions (to avoid code duplication).
     *
     * @param Language $language
     */
    private function initForm(Language $language = null)
    {
        if (!$language instanceof Language) {
            $language = new Language([]);
        }

        /**
         * @var $form LanguageForm
         */
        $form = $this->languageForm;
        $form->bind($language);
        $this->getView()->form = $form;
        if ($this->getRequest()->isPost()) {
            $form->setInputFilter($form->getInputFilter());
            $form->setData($this->getRequest()->getPost());
            if ($form->isValid()) {
                $this->languageTable->saveLanguage($language);
                return $this->setLayoutMessages($this->translate("LANGUAGE")." &laquo;".$language->getName()."&raquo; ".$this->translate("SAVE_SUCCESS"), 'success');
            }
            return $this->setLayoutMessages($form->getMessages(), 'error');
        }
    }
}
