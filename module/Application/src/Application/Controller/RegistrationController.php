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

use Application\Form\RegistrationForm;
use Custom\Plugins\Functions;

class RegistrationController extends IndexController
{
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

        $form = new RegistrationForm(['action' => '/registration/processregistration', 'method' => 'post']);
        $form->setInputFilter($form->getInputFilter());
        $form->setData($this->getRequest()->getPost());

        if ($form->isValid()) {
            $formData = $form->getData();
            $remote = new \Zend\Http\PhpEnvironment\RemoteAddress();

            $existingEmail = $this->getTable("user")->fetchList(false, "email = '".$formData['email']."'");
            (count($existingEmail) > 0 ? $this->setErrorNoParam($this->translation->EMAIL_EXIST." <b>".$formData["email"]."</b> ".$this->translation->ALREADY_EXIST) : "");

            $registerUser = new \Admin\Model\User();
            $registerUser->setName($formData['name']);
            $registerUser->setPassword(Functions::createPassword($formData["password"]));
            $registerUser->setSalt(""); // remove me
            $registerUser->setRegistered(date("Y-m-d H:i:s", time()));
            $registerUser->setIp($remote->getIpAddress());
            $registerUser->setEmail($formData['email']);
            $registerUser->setLanguage($this->translation->language);
            $this->getTable("user")->saveUser($registerUser);
            $this->cache->success = $this->translation->REGISTRATION_SUCCESS;
            return $this->redirect()->toUrl("/login");
        } else {
            $this->formErrors($form->getMessages());
            return $this->redirect()->toUrl("/registration");
        }
    }

    public function indexAction()
    {
        $form = new RegistrationForm(['action' => '/registration/processregistration', 'method' => 'post']);
        $form->get("name")->setLabel($this->translation->NAME)->setAttribute("placeholder", $this->translation->NAME);
        $form->get("email")->setLabel($this->translation->EMAIL);
        $form->get("password")->setLabel($this->translation->PASSWORD);
        $form->get("repeatpw")->setLabel($this->translation->REPEAT_PASSWORD)->setAttribute("placeholder", $this->translation->REPEAT_PASSWORD);
        $form->get("captcha")->setLabel($this->translation->CAPTCHA)->setAttribute("placeholder", $this->translation->ENTER_CAPTCHA);
        $form->get("register")->setValue($this->translation->CREATE_ACCOUNT);

        $this->view->form = $form;
        return $this->view;
    }
}
