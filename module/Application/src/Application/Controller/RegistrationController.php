<?php
namespace Application\Controller;

use Application\Controller\IndexController;
use Application\Form\RegistrationForm;
use Custom\Plugins\Functions;
use Zend\Http\PhpEnvironment\RemoteAddress;

class RegistrationController extends IndexController
{
    public function onDispatch(\Zend\Mvc\MvcEvent $e)
    {
        parent::onDispatch($e);
        $this->checkIdentity();
    }

    public function processregistrationAction()
    {
        if(!$this->getRequest()->isPost())
        {
            $this->errorNoParam();
        }

        $form = new RegistrationForm(array('action' => '/registration/processregistration','method' => 'post'));
        $form->setInputFilter($form->getInputFilter());
        $form->setData($this->getRequest()->getPost());
        
        if($form->isValid())
        {
            $formData = $form->getData();
            $existingEmail = $this->getTable("user")->fetchList(false, "email = '".$formData['email']."'");
            (count($existingEmail) > 0 ? $this->errorNoParam($this->translation->EMAIL_EXIST." <b>".$formData["email"]."</b> ".$this->translation->ALREADY_EXIST) : "");
            
            if(count($existingEmail) === 0)
            {
                $registerUser = new \Admin\Model\User();
                $registerUser->setName($formData['name']);
                $pw = Functions::createPassword($formData["password"]);
                if (!empty($pw))
                {
                    $registerUser->setPassword($pw);
                    $registerUser->setSalt("");
                    $registerUser->setRegistered(date("Y-m-d H:i:s", time()));
                    $remote = new RemoteAddress();
                    $registerUser->setIp($remote->getIpAddress());
                    $registerUser->setEmail($formData['email']);
                    $registerUser->setLanguage($this->translation->language);
                    $this->getTable("user")->saveUser($registerUser);
                    $this->cache->success = $this->translation->REGISTRATION_SUCCESS;
                    return $this->redirect()->toUrl("/login");
                }
                throw new Exception\RuntimeException("Password could not be generated.");
            }
        }
        else
        {
            $error = array();
            foreach($form->getMessages() as $msg)
            {
                foreach ($msg as $key => $value)
                {
                    $error[] = $value;
                }
            }
            $this->errorNoParam($error);
            return $this->redirect()->toUrl("/registration");
        }
    }

    public function indexAction()
    {
        $form = new RegistrationForm(array('action' => '/registration/processregistration','method' => 'post'));
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
?>