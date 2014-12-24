<?php
namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Cache\Storage\Adapter\Session;
use Zend\View\Model\ViewModel;
use Zend\Session\Container;

use Custom\Error\AuthorizationException;
use Custom\Plugins\Functions;
use Custom\Plugins\Mailing;

class IndexController extends AbstractActionController
{
    /**
     * @param null $cache holds any other session information, contains warning, success and error vars that are shown just once and then reset
     * @return Zend\Session\Container|mixed
     */
    public $cache = null;

    /**
     * @param null $view creates instance to view model
     * @return Zend\View\Model\ViewModel
     */
    public $view = null;

    /**
     * @param null $translation holds language data as well as all translations
     * @return Zend\Session\Container
     */
    public $translation = null;
    
    /**
     * constructor
     */
    public function __construct()
    {
        $this->view = new ViewModel();
        $this->translation = new Container('translations');
        $this->initCache();
    }

    /**
     * Initialize any variables before controller actions
     * @throws Exception\RuntimeException
     */
    public function onDispatch(\Zend\Mvc\MvcEvent $e)
    {
        parent::onDispatch($e);

        if(!class_exists("Admin\Model\Language"))
        {
            throw new Exception\RuntimeException($this->translation->LANGUAGE_CLASS_NOT_FOUND);
        }
        $this->initLanguages();
        $this->initViewVars();

        // Store all menus in a variable for the front page
        $temp = $this->getTable("Menu")->fetchList(false, "parent='0' AND menutype='0' AND language='".$this->translation->language."'", "menuOrder ASC");
        $menus = $submenus = array();
        $menuId = $submenuId = null;

        foreach($temp as $m)
        {
            $menus[] = $m;
            $submenus[$m->id] = $this->getTable("Menu")->fetchList(false, "parent='" . $m->id."' AND menutype='0' AND language='".$this->translation->language."'", "menuOrder ASC");
        }
        $this->view->menus = $menus;
        $this->view->submenus = $submenus;
        return $this->view;
    }

/****************************************************
 * START OF ALL INIT FUNCTIONS
 ****************************************************/
    /**
     * initialize any session variables in this method
     * 
     * @return Zend\Session\Container
     */
    public function initCache()
    {
        if (empty($this->cache))
        {
            $this->cache = new Container("cache");
            $this->view->cache = $this->cache;
        }
    }

    /**
     * initialize any view related stuff
     *
     * @return Zend\Session\Container
     */
    public function initViewVars()
    {
        $this->view->translation = $this->translation;
        $this->view->languages = $this->getTable("Language")->fetchList(false, "active='1'", "name ASC");
        $this->view->languageId = (int) $this->translation->language;
        $this->view->controllerShort = strtolower(substr($this->params('controller'), strrpos($this->params('controller'),"\\")+1));
        $this->view->controllerLong = $this->params('controller');
        $this->view->action = $this->params('action');
        $this->view->baseURL = $this->getRequest()->getRequestUri();
    }

    /** 
     * initialize languages and language-related stuff like translations.
     */
    public function initLanguages()
    {
        $this->translation = new Container('translations');
        if(!empty($this->translation->language))
        {
            $this->view->language = $this->getTable("Language")->getLanguage($this->translation->language);
            $this->translation = Functions::initTranslations($this->translation->language, false);
        }
        $this->translation->language = 1;
        $this->translation = Functions::initTranslations($this->translation->language, true);
    }

/****************************************************
 * START OF ALL MAIN/SHARED FUNCTIONS
 ****************************************************/

    /**
     * @param String $name
     * @return Ambigous <object, multitype:>
     */
    public function getTable($name = null)
    {
        if (!is_string($name) || empty($name))
        {
            throw new Exception\InvalidArgumentException(__METHOD__ . ' must be string and must not be empty');
        }
        return $this->getServiceLocator()->get($name . "Table");
    }

    /**
     * See if user is logged in.
     * Crazy logic, but it does the trick.
     *
     * @see \Admin\Model\User
     * @see \Application\Controller\LoginController for roles
     * @throws AuthorizationException
     * @return void
     */
    public function checkIdentity()
    {
        if (is_object($this->cache) && isset($this->cache) && $this->cache instanceof \Zend\Session\Container)
        {
            if(is_object($this->cache->user) && isset($this->cache->user) && $this->cache->user instanceof \Admin\Model\User)
            {
                if(($this->cache->role === 1 || $this->cache->role === 10) && $this->cache->logged)
                {
                    return $this->redirect()->toUrl("/");
                }
                else
                {
                    $this->cache->getManager()->getStorage()->clear();
                    $this->translation->getManager()->getStorage()->clear();
                    $this->cache = new Container("cache");
                    $this->translation = new Container("translations");
                    $authSession = new Container('ul');
                    $authSession->getManager()->getStorage()->clear();
                    throw new AuthorizationException($this->translation->ERROR_AUTHORIZATION);
                }
            }
        }
    }

    /**
     * Shorthand method for getting params from URLs. Makes code easier to modify and avoids DRY code
     *
     * @param String $paramName
     * @param null $default
     * @return mixed
     */
    protected function getParam($paramName, $default = null)
    {
        $param = $this->params()->fromPost($paramName, 0);
        if(!$param) 
        {
            $param = $this->params()->fromRoute($paramName, 0);
        }
        if(!$param)
        {
            return $default;
        }
        else
        {
            return $param;
        }
    }

    /**
     * @param null $message holds the generated error
     * @return string|array
     */
    public function errorNoParam($message = null)
    {
        if(!empty($message))
        {
            $this->cache->error = $message;
        }
        else if ($message === 'no_id')
        {
            $this->cache->error = $this->translation->NO_ID_SET;
        }
        else
        {
            $this->cache->error = $this->translation->ERROR_STRING;
        }
        $this->view->setTerminal(true);
    }

    /**
     * @param  null $param is the menu/controller name passed as string from the URL
     * @return array containting menu ids
     */
    public function matchSEOMenu($param = null)
    {
        if (empty($param))
        {
           throw new Exception\InvalidArgumentException(__METHOD__ . ' must not be empty');
        }

        // this is needed due to the friendly/RESTful urls
        // check to see if menu caption with - or _ exist (we check for the whole menu caption), if not, replace them and check again
        $matches = $this->getTable("Menu")->fetchList(false, "caption = '{$param}' AND language='".$this->translation->language."'");
        if (!$matches->current())
        {
            $param = str_replace(array("-","_"), array(" ","/"), $param);
            $matches = $this->getTable("Menu")->fetchList(false, "caption LIKE '%{$param}%' AND language='".$this->translation->language."'");
        }

        if(count($matches) > 0)
        {
            $location = array("menu" => null, "submenu" => null);
            $match = $matches->current();
            if(!$match->getParent())
            {
                $location["menu"] = (int) $match->getId();
            }
            else
            {
                $location["submenu"] = (int) $match->getId();
                $location["menu"] = (int) $match->getParent();
            }
            return $location;
        }
        return false;
    }

    public function setMetaTags($obj = null, $isPage = true)
    {
        $description = $keywords = $extract = $preview = $title = null;

        if (empty($obj))
        {
            return false;
        }
        if ($isPage === true)
        {
            $description = $obj->current()->getMenuObject()->getDescription();
            $keywords = $obj->current()->getMenuObject()->getKeywords();
            $extract = $obj->current()->getExtract();
            $preview = $obj->current()->getPreview();
            $title = $obj->current()->getTitle();
        }
        else if ($isPage === "news")
        {

                // foreach ($obj as $value)
                // {
                //     // $value->setServiceManager(null);
                //     // echo \Zend\Debug\Debug::dump($value, null, true, true);exit;
                //     $extract = $value->getExtract();
                //     $preview = $value->getPreview();
                //     $title = $value->getTitle(); //cannot be empty

                //     (empty($extract) ? $extract = $value->getText() : $extract);
                // }
            
        }
        else
        {
            $description = $obj->current()->getMenuObject()->getDescription();
            $keywords = $obj->current()->getMenuObject()->getKeywords();
            $extract = $obj->current()->getExtract();
            $preview = $obj->current()->getPreview();
            $title = $obj->current()->getTitle(); //cannot be empty
        }
        // (empty($extract) ? $extract = $obj->getText() : $extract);
        (empty($description) ? $description = "test desc" : $description);
        (empty($keywords) ? $keywords = "test keyw" : $keywords);
        (empty($preview) ? $preview = "" : $preview);

        $hm = $this->getServiceLocator()->get('ViewHelperManager')->get('headMeta');
        $placeholder = $this->getServiceLocator()->get('ViewHelperManager')->get('placeholder');
        $placeholder->getContainer("customHead")->append("<meta itemprop='name' content='ZendPress'>\r\n");
        $placeholder->getContainer("customHead")->append("<meta itemprop='description' content='".substr(strip_tags($extract), 0, 100)."..."."'>\r\n");
        $placeholder->getContainer("customHead")->append("<meta itemprop='title' content='".$title."'>\r\n");
        $placeholder->getContainer("customHead")->append("<meta itemprop='image' content='".$preview."'>\r\n");
        
        $hm->appendName('keywords', $keywords);
        $hm->appendName('description', $description);
        $hm->appendProperty('og:image', $preview);
        $hm->appendProperty($title, "og:title");
        $hm->appendProperty($description, "og:description");
    }

/****************************************************
 * START OF ALL ACTION METHODS
 ****************************************************/

    public function indexAction()
    {
        return $this->view;
    }

    /**
     * What happens here is that pageAction was designed to search for menu captions only,
     * but sometimes we are trying to avoid searching for specific controllers as menus such as LoginController.
     * So we are using array_key_exists to check if we are in such controller.
     * (where the key is the controller name and the value is the controller action we want to rdirect).
     * After that we get the url from the bar and try to find an action with the given name.
     *
     * @param array $otherControllers holds all controllers except the IndexController
     * @return controller and/or action
     */
    public function pageAction()
    {

        // $otherControllers = array(
        //     // 'registration' => array('processregistration'),
        //     // 'login' => array('processlogin', 'logout', 'resetpassword', 'newpasswordprocess'),
        //     // 'profile' => array('settings'),
        // );
        // if (array_key_exists(strtolower($param), $otherControllers))
        // {
        //     foreach ($otherControllers as $controllerName => $actionName)
        //     {
        //         $separatedURL = explode("/", $this->getRequest()->getRequestUri());
        //         if (in_array($separatedURL[2], $actionName)) 
        //         {
        //             return $this->forward()->dispatch('Application\Controller\\'.ucfirst($controllerName), array('action' => $separatedURL[2]));
        //         }
        //     }
        // }
        // else
        // {
            $param = (string) $this->getParam("param");
            $submenuId = (int) $this->getParam('id');
            $menu = $submenu = $contents = null;

            if ($submenuId && is_int($submenuId))
            {
                $this->view->hideMainMenu = true;
                try
                {
                    $contents = $this->getTable("Content")->fetchList(false, "id='{$submenuId}' AND language='".$this->translation->language."'", "menuOrder ASC");

                    // $menuVisCaption = $contents->current()->getMenuObject()->getCaption();
                    // $menuCaption = strtolower(str_replace(" ", "-", $menuVisCaption));
                    // $aaa = $contents->current()->getMenuObject()->getParentObject();

                    // if ($aaa)
                    // {
                    //     $parentCaption = $aaa->getCaption();
                    //     $parentMenuCaption = strtolower(str_replace(" ", "-", $parentCaption));
                    //     $this->view->parentMenuSelected = $aaa;
                    // }
                    // else
                    // {
                    //     $this->view->menuSelected = $contents->current()->getMenuObject();
                    // }
                }
                catch(\Exception $e)
                {
                    throw new \Exception("An error has occurred");
                }
            }
            else
            {
                $this->view->hideMainMenu = false;
                if(Functions::strLength($param) > 0)
                {
                    $menu = $this->matchSEOMenu($param);
                }
                else
                {
                    $this->getResponse()->setStatusCode(404);
                    $this->view->setTemplate('layout/error-layout');
                    return $this->view;
                }
                try
                {
                    if (!empty($menu["submenu"]))
                    {
                        $contents = $this->getTable("Content")->fetchList(false, "menu='{$menu["submenu"]}' AND language='".$this->translation->language."'", "menuOrder ASC");
                    }
                    else if(!empty($menu["menu"]))
                    {
                        $contents = $this->getTable("Content")->fetchList(false, "menu='{$menu["menu"]}' AND language='".$this->translation->language."'", "menuOrder ASC");
                    }
                }
                catch(\Exception $e)
                {
                    throw new \Exception("An error has occurred");
                }
            }

            $this->setMetaTags($contents);
            $this->view->contents = $contents;
            return $this->view;
        // }
    }

    public function contactAction()
    {
        $form = new \Application\Form\ContactForm();
        $form->get("email")->setLabel($this->translation->EMAIL);
        $form->get("name")->setLabel($this->translation->NAME)->setAttribute("placeholder", $this->translation->NAME);
        $form->get("subject")->setLabel($this->translation->SUBJECT)->setAttribute("placeholder", $this->translation->SUBJECT);
        $form->get("captcha")->setLabel($this->translation->CAPTCHA)->setAttribute("placeholder", $this->translation->ENTER_CAPTCHA);
        $form->get("message")->setLabel($this->translation->MESSAGE)->setAttribute("placeholder", $this->translation->ENTER_MESSAGE);
       
        $this->view->form = $form;
        if($this->getRequest()->isPost())
        {
            $form->setInputFilter($form->getInputFilter());
            $form->setData($this->getRequest()->getPost());
            if($form->isValid())
            {
                $formData = $form->getData();
                $to = "stanimirdim92@gmail.com"; // must be set from db
                $result = Mailing::sendMail($to, '', $formData['subject'], $formData['message'], $formData['email'], $formData['name']);
                if ($result)
                {
                    $this->cache->success = $this->translation->CONTACT_SUCCESS;
                    return $this->redirect()->toUrl("/");
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
                return $this->redirect()->toUrl("/");
            }
        }
        return $this->view;
    }
}
?>