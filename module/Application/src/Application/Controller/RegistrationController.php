<?php

/**
 * @copyright  2015 (c) Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 *
 * @version    0.0.14
 *
 * @link       TBA
 */

namespace Application\Controller;

use Admin\Model\User;
use Zend\Http\PhpEnvironment\RemoteAddress;
use Zend\Mvc\MvcEvent;
use Application\Form\RegistrationForm;

final class RegistrationController extends IndexController
{
    /**
     * @var RegistrationForm $registrationForm
     */
    private $registrationForm = null;

    /**
     * @param RegistrationForm $registrationForm
     */
    public function __construct(RegistrationForm $registrationForm = null)
    {
        parent::__construct();
        $this->registrationForm = $registrationForm;
    }

    /**
     * @param MvcEvent $e
     */
    public function onDispatch(MvcEvent $e)
    {
        parent::onDispatch($e);

        /**
         * If user is logged and tries to access one of the given actions
         * he will be redirected to the root url of the website.
         * For resetpassword and newpassword actions we assume that the user is not logged in.
         */
        if (APP_ENV !== 'development') {
            $this->UserData()->checkIdentity();
        }
    }

    public function processregistrationAction()
    {
        if (!$this->getRequest()->isPost()) {
            return $this->redirect()->toUrl("/registration");
        }

        /**
         * @var RegistrationForm $form
         */
        $form = $this->registrationForm;

        $form->setInputFilter($form->getInputFilter());
        $form->setData($this->getRequest()->getPost());

        if ($form->isValid()) {
            $formData = $form->getData();
            $remote = new RemoteAddress();

            /*
             * See if there is already registered user with this email
             */
            $existingEmail = $this->getTable("user")->fetchList(false, [], ["email" => $formData["email"]]);
            if (count($existingEmail) > 0) {
                return $this->setLayoutMessages($this->translate("EMAIL_EXIST")." <b>".$formData["email"]."</b> ".$this->translate("ALREADY_EXIST"), 'info');
            } else {
                $func = $this->getFunctions();
                $registerUser = new User();
                $registerUser->setName($formData["name"]);
                $registerUser->setPassword($func::createPassword($formData["password"]));
                $registerUser->setRegistered(date("Y-m-d H:i:s", time()));
                $registerUser->setIp($remote->getIpAddress());
                $registerUser->setEmail($formData["email"]);
                $registerUser->setLanguage($this->language());
                $this->getTable("user")->saveUser($registerUser);
                $this->setLayoutMessages($this->translate("REGISTRATION_SUCCESS"), 'success');
                return $this->redirect()->toUrl("/login");
            }
        } else {
            $this->setLayoutMessages($form->getMessages(), 'error');
            return $this->redirect()->toUrl("/registration");
        }
    }

    /**
     * @return ViewModel
     */
    public function indexAction()
    {
        $this->getView()->setTemplate("application/registration/index");

        /**
         * @var RegistrationForm $form
         */
        $form = $this->registrationForm;

        $form->get("name")->setLabel($this->translate("NAME"))->setAttribute("placeholder", $this->translate("NAME"));
        $form->get("email")->setLabel($this->translate("EMAIL"));
        $form->get("password")->setLabel($this->translate("PASSWORD"));
        $form->get("repeatpw")->setLabel($this->translate("REPEAT_PASSWORD"))->setAttribute("placeholder", $this->translate("REPEAT_PASSWORD"));
        $form->get("captcha")->setLabel($this->translate("CAPTCHA"))->setAttribute("placeholder", $this->translate("ENTER_CAPTCHA"));
        $form->get("register")->setValue($this->translate("SIGN_UP"));

        $this->getView()->form = $form;
        return $this->getView();
    }
}
