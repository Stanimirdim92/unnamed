<?php

/**
 * @copyright  2015 (c) Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 *
 * @version    0.0.18
 *
 * @link       TBA
 */

namespace Admin\Controller;

use Admin\Form\SettingsMailForm;
use Admin\Form\SettingsPostsForm;
use Admin\Form\SettingsGeneralForm;
use Admin\Form\SettingsDiscussionForm;
use Admin\Form\SettingsRegistrationForm;

final class SettingsController extends IndexController
{
    /**
     * @var SettingsMailForm
     */
    private $mailForm = null;

    /**
     * @var SettingsPostsForm
     */
    private $postsForm = null;

    /**
     * @var SettingsGeneralForm
     */
    private $generalForm = null;

    /**
     * @var SettingsDiscussionForm
     */
    private $discussionForm = null;

    /**
     * @var SettingsRegistrationForm
     */
    private $registrationForm = null;

    /**
     * @method __construct
     *
     * @param SettingsMailForm $mailForm
     * @param SettingsPostsForm $postsForm
     * @param SettingsGeneralForm $generalForm
     * @param SettingsDiscussionForm $discussionForm
     * @param SettingsRegistrationForm $registrationForm
     */
    public function __construct(
        SettingsMailForm $mailForm = null,
        SettingsPostsForm $postsForm = null,
        SettingsGeneralForm $generalForm = null,
        SettingsDiscussionForm $discussionForm = null,
        SettingsRegistrationForm $registrationForm = null
    ) {
        parent::__construct();

        $this->mailForm = $mailForm;
        $this->postsForm = $postsForm;
        $this->generalForm = $generalForm;
        $this->discussionForm = $discussionForm;
        $this->registrationForm = $registrationForm;
    }

    /**
     * @param MvcEvent $e
     */
    public function onDispatch(\Zend\Mvc\MvcEvent $e)
    {
        parent::onDispatch($e);
        $this->addBreadcrumb(["reference"=>"/admin/settings", "name"=>$this->translate("SETTINGS")]);
    }

    /**
     * @return ViewModel
     */
    protected function generalAction()
    {
        $this->getView()->setTemplate("admin/settings/general");

        $this->initForm($this->generalForm);

        return $this->getView();
    }

    /**
     * @return ViewModel
     */
    protected function registrationAction()
    {
        $this->getView()->setTemplate("admin/settings/registration");

        $this->initForm($this->registrationForm, 'registration');

        return $this->getView();
    }

    /**
     * @return ViewModel
     */
    protected function mailAction()
    {
        $this->getView()->setTemplate("admin/settings/mail");

        $this->initForm($this->mailForm, 'mail');

        return $this->getView();
    }

    /**
     * @return ViewModel
     */
    protected function postsAction()
    {
        $this->getView()->setTemplate("admin/settings/posts");

        $this->initForm($this->postsForm, 'posts');

        return $this->getView();
    }

    /**
     * @return ViewModel
     */
    protected function discussionAction()
    {
        $this->getView()->setTemplate("admin/settings/discussion");

        $this->initForm($this->discussionForm, 'discussion');

        return $this->getView();
    }

    /**
     * @param $form object
     * @param $action string
     */
    private function initForm($form, $actionKey = 'general')
    {
        $form->get("submit")->setValue($this->translate("EDIT"));
        $filename = "config/autoload/system.local.php";
        $settings = include $filename;
        $this->getView()->form = $form;

        if ($this->getRequest()->isPost()) {
            $form->setInputFilter($form->getInputFilter());
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $formData = $form->getData();

                unset($formData["submit"], $formData["s"]);
                $settings["system_config"][$actionKey] = array_merge($settings["system_config"][$actionKey], $formData);

                file_put_contents($filename, '<?php return '.var_export($settings, true).';');
                $this->setLayoutMessages($this->translate("SETTINGS")." ".$this->translate("SAVE_SUCCESS"), 'success');
            } else {
                $this->setLayoutMessages($form->getMessages(), 'error');
            }
        }
    }
}
