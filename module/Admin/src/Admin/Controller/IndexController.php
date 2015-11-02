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

use Admin\Controller\BaseController;

final class IndexController extends BaseController
{
    /**
     * @return \Zend\View\Model\ViewModel
     */
    public function indexAction()
    {
        $this->getView()->setTemplate("admin/index/index");

        return $this->getView();
    }

    /**
     * Select new language.
     *
     * This will reload the translations every time the method is being called.
     */
    protected function languageAction()
    {
        $language = $this->getTable("Admin\\Model\\LanguageTable")->getLanguage((int) $this->getParam("id", 1));

        $this->getTranslation()->language = $language->getId();
        $this->getTranslation()->languageName = $language->getName();

        return $this->redirect()->toUrl("/");
    }
}
