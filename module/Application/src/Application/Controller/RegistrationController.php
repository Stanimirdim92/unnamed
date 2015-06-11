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
 * @category   Application\Registration
 * @package    ZendPress
 * @author     Stanimir Dimitrov <stanimirdim92@gmail.com>
 * @copyright  2015 Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 * @version    0.03
 * @link       TBA
 */
namespace Application\Controller;

use Custom\Plugins\Functions;
use Admin\Model\User;
use Zend\Http\PhpEnvironment\RemoteAddress;

class RegistrationController extends IndexController
{

    /**
     * @var Application\Form\RegistrationForm $registrationForm
     */
    private $registrationForm = null;

    /**
     * @param  Application\Form\RegistrationForm $registrationForm
     */
    public function __construct(\Application\Form\RegistrationForm $registrationForm = null)
    {
        parent::__construct();
        $this->registrationForm = $registrationForm;
    }

    /**
     * @param MvcEvent $e
     */
    public function onDispatch(\Zend\Mvc\MvcEvent $e)
    {
        parent::onDispatch($e);
        $this->checkIdentity();
    }

    public function processregistrationAction()
    {
        if (!$this->getRequest()->isPost()) {
            return $this->redirect()->toUrl("/registration");
        }

        $form = $this->registrationForm;
        $form->setInputFilter($form->getInputFilter());
        $form->setData($this->getRequest()->getPost());

        if ($form->isValid()) {
            $formData = $form->getData();
            $remote = new RemoteAddress();

            $existingEmail = $this->getTable("user")->fetchList(false, "email = '".$formData['email']."'");
            (count($existingEmail) > 0 ? $this->setLayoutMessages($this->translate("EMAIL_EXIST")." <b>".$formData["email"]."</b> ".$this->translate("ALREADY_EXIST"), 'info') : "");

            $registerUser = new User();
            $registerUser->setName($formData['name']);
            $registerUser->setPassword(Functions::createPassword($formData["password"]));
            $registerUser->setSalt(""); // remove me
            $registerUser->setRegistered(date("Y-m-d H:i:s", time()));
            $registerUser->setIp($remote->getIpAddress());
            $registerUser->setEmail($formData['email']);
            $registerUser->setLanguage($this->translation->language);
            $this->getTable("user")->saveUser($registerUser);
            $this->setLayoutMessages($this->translate("REGISTRATION_SUCCESS"), 'success');
            return $this->redirect()->toUrl("/login");
        } else {
            $this->setLayoutMessages($form->getMessages(), 'success');
            return $this->redirect()->toUrl("/registration");
        }
    }

    public function indexAction()
    {
        $form = $this->registrationForm;
        $form->get("name")->setLabel($this->translate("NAME"))->setAttribute("placeholder", $this->translate("NAME"));
        $form->get("email")->setLabel($this->translate("EMAIL"));
        $form->get("password")->setLabel($this->translate("PASSWORD"));
        $form->get("repeatpw")->setLabel($this->translate("REPEAT_PASSWORD"))->setAttribute("placeholder", $this->translate("REPEAT_PASSWORD"));
        $form->get("captcha")->setLabel($this->translate("CAPTCHA"))->setAttribute("placeholder", $this->translate("ENTER_CAPTCHA"));
        $form->get("register")->setValue($this->translate("CREATE_ACCOUNT"));

        $this->view->form = $form;
        return $this->view;
    }
}
