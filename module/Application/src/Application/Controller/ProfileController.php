<?php
namespace Application\Controller;

use Application\Controller\IndexController;
use Application\Form\UserSettingsForm;
use Cusomt\Plugins\Functions;

class ProfileController extends IndexController
{
    public function onDispatch(\Zend\Mvc\MvcEvent $e)
    {
        parent::onDispatch($e);
    }

    public function settingsAction()
    {
        if (!isset($this->cache->user) && !$this->cache->logged)
        {
            return $this->redirect()->toUrl("/");
        }

        $user = $this->cache->user;

        $form = new UserSettingsForm($user);
        $form->get("submit")->setValue($this->translation->SAVE_SETTINGS);
        $form->get("name")->setLabel($this->translation->NAME)->setAttribute("placeholder", $this->translation->NAME);
        $form->get("password")->setLabel($this->translation->PASSWORD);
        $form->get("surname")->setLabel($this->translation->SURNAME)->setAttribute("placeholder", $this->translation->SURNAME);
        $form->get("email")->setLabel($this->translation->EMAIL);
        $form->get("birthDate")->setLabel($this->translation->BIRTHDATE);

        $this->view->form = $form;
        $this->view->id = $user->id;
        if($this->getRequest()->isPost())
        {
            $form->setInputFilter($form->getInputFilter());
            $form->setData($this->getRequest()->getPost());
            if($form->isValid())
            {
                $formData = $form->getData();
                // $name = str_replace(" ", "_", $formData["name"]);
                // $existingUser = $this->getTable("user")->fetchList(false, "name = '{$name}' AND id != '{$user->id}'");
                $existingEmail = $this->getTable("user")->fetchList(false, "email = '".$formData['email']."' AND id != '{$user->id}'");
                // (count($existingUser) > 0 ? $this->errorNoParam($this->translation->NAME_EXIST." <b>{$name}</b> ".$this->translation->ALREADY_EXIST) : "");
                (count($existingEmail) > 0 ? $this->errorNoParam($this->translation->EMAIL_EXIST." <b>".$formData["email"]."</b> ".$this->translation->ALREADY_EXIST) : "");

                if(count($existingEmail) === 0 /*&& count($existingUser) == 0*/)
                {
                    $user->setName($formData["name"]);
                    $user->setSurname($formData['surname']);
                    if (empty($formData['password']))
                    {
                        $user->setPassword($user->password);
                    }
                    else
                    {
                        $pw = Functions::createPassword($formData['password']);
                        $user->setPassword($pw);
                        $user->setSalt("");
                    }
                    $user->setEmail($formData['email']);
                    $user->setBirthDate($formData["birthDate"]);
                    $this->getTable("user")->saveUser($user);
                    $this->cache->success = $this->translation->SETTINGS_SUCCESS;
                    $this->view->setTerminal(true);
                }
                return $this->redirect()->toUrl("/");
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
                return $this->redirect()->toUrl("/");
            }
        }
        return $this->view;
    }
}
?>