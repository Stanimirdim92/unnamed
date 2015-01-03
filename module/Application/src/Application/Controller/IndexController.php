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
    public $langTranslation = null;
    
    /**
     * constructor
     */
    public function __construct()
    {
        $this->view = new ViewModel();
        $this->translation = new Container('translations');
        $this->initCache();
        // keeping it simple and DRY
        $this->langTranslation = ((int) $this->translation->language ? (int) $this->translation->language : 1);
    }

    /**
     * Initialize any variables before controller actions
     * @param \Zend\Mvc\MvcEvent $e
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
        $temp = $this->getTable("Menu")->fetchList(false, "parent='0' AND menutype='0' AND language='".$this->langTranslation."'", "menuOrder ASC");
        $menus = $submenus = array();
        $menuId = $submenuId = null;

        foreach($temp as $m)
        {
            $menus[] = $m;
            $submenus[$m->id] = $this->getTable("Menu")->fetchList(false, "parent='" . $m->id."' AND menutype='0' AND language='".$this->langTranslation."'", "menuOrder ASC");
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
        $this->view->languageId = $this->langTranslation;
        $this->view->controllerShort = strtolower('__CONTROLLER__');
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
        if(empty($this->translation->language))
        {
            $this->translation->language = 1;
            $this->translation = Functions::initTranslations($this->translation->language, true);
        }
        else
        {
            $this->view->language = $this->getTable("Language")->getLanguage($this->translation->language);
            $this->translation = Functions::initTranslations($this->translation->language);
        }
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
     * @see \Application\Controller\LoginController
     * @throws AuthorizationException
     * @return void
     */
    protected function checkIdentity()
    {
        if(isset($this->cache->user) && $this->cache->user instanceof \Admin\Model\User)
        {
            $auth = new \Zend\Authentication\AuthenticationService();
            if($auth->hasIdentity())
            {
                if( (($auth->getIdentity()->role === 1 || $auth->getIdentity()->role === 10) && $this->cache->logged) && 
                    (($this->cache->role === 1 || $this->cache->role === 10) && $this->cache->logged))
                {
                    return $this->redirect()->toUrl("/");
                }
            }
            else
            {
                $this->cache->getManager()->getStorage()->clear();
                $this->translation->getManager()->getStorage()->clear();
                $authSession = new Container('ul');
                $authSession->getManager()->getStorage()->clear();
                $auth->clearIdentity();
                unset($this->cache->user);
                unset($authSession);
                $this->cache = null;
                throw new AuthorizationException($this->translation->ERROR_AUTHORIZATION);
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
    protected function getParam($paramName = null, $default = null)
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
        return $param;
    }

    /**
     * @param null $message holds the generated error
     * @return string|array
     */
    protected function errorNoParam($message = null)
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
        $matches = $this->getTable("Menu")->fetchList(false, "caption = '{$param}' AND language='".$this->langTranslation."'");
        if (!$matches->current())
        {
            $param = str_replace(array("-","_"), array(" ","/"), $param);
            $matches = $this->getTable("Menu")->fetchList(false, "caption LIKE '%{$param}%' AND language='".$this->langTranslation."'");
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

        /**
         * This section is called when we enter the pageAction and request a menu.
         */
        if ($isPage === true)
        {
            $description = $obj->current()->getMenuObject()->getDescription();
            $keywords = $obj->current()->getMenuObject()->getKeywords();
            $extract = $obj->current()->getExtract();
            $preview = $obj->current()->getPreview();
            $title = $obj->current()->getTitle();
            (empty($extract) ? $extract = $obj->current()->getText() : $extract);
        }
        /**
         * This section is called when we request newspost. 
         *
         * @see Application\Controller\NewsController
         */
        else if ($isPage === "news")
        {
            $extract = $obj->current()->getExtract();
            $preview = $obj->current()->getPreview();
            $title = $obj->current()->getTitle();
            (empty($extract) ? $extract = $obj->current()->getText() : $extract);
        }
        /**
         * All other pages? Maybe load the text from the database
         */
        else
        {
            $description = $obj->current()->getMenuObject()->getDescription();
            $keywords = $obj->current()->getMenuObject()->getKeywords();
            $extract = $obj->current()->getExtract();
            $preview = $obj->current()->getPreview();
            $title = $obj->current()->getTitle(); //cannot be empty
        }
        
        (empty($description) ? $description = "test desc" : $description);
        (empty($keywords) ? $keywords = "test keyw" : $keywords);
        (empty($preview) ? $preview = "" : $preview);

        $hm = $this->getServiceLocator()->get('ViewHelperManager')->get('headMeta');
        $placeholder = $this->getServiceLocator()->get('ViewHelperManager')->get('placeholder');
        $placeholder->getContainer("customHead")->append("<meta itemprop='name' content='ZendPress'>\r\n");
        // TODO: clear the new lines from the text. See the source code ctrl+u
        $placeholder->getContainer("customHead")->append("<meta itemprop='description' content='".substr(strip_tags($extract), 0, 100)."..."."'>\r\n");
        $placeholder->getContainer("customHead")->append("<meta itemprop='title' content='".$title."'>\r\n");
        $placeholder->getContainer("customHead")->append("<meta itemprop='image' content='".$preview."'>\r\n");
        
        $hm->appendName('keywords', $keywords);
        $hm->appendName('description', $description);
        $hm->appendProperty('og:image', $preview);
        $hm->appendProperty("og:title", $title);
        $hm->appendProperty("og:description", $description);
    }

/****************************************************
 * START OF ALL ACTION METHODS
 ****************************************************/

    public function indexAction()
    {
        return $this->view;
    }

    /**
     * Get the contents for the menu/submenu
     *
     * @return mixed Object holding the menu text, name etc.
     */
    public function pageAction()
    {
        $param = (string) $this->getParam("param");
        $submenuId = (int) $this->getParam('id');

        if ($submenuId && is_int($submenuId))
        {
            $this->view->hideMainMenu = true;
            try
            {
                $contents = $this->getTable("Content")->fetchList(false, "id='{$submenuId}' AND language='".$this->langTranslation."'", "menuOrder ASC");
            }
            catch(\Exception $e)
            {
                throw new \Exception("An error has occurred");
            }
        }
        else
        {
            $this->view->hideMainMenu = false;
            if(Functions::strLength($param) === 0)
            {
                $this->getResponse()->setStatusCode(404);
                $this->view->setTemplate('layout/error-layout');
                return $this->view;
            }
            $menu = $this->matchSEOMenu($param);
            if (!empty($menu["submenu"]))
            {
                $contents = $this->getTable("Content")->fetchList(false, "menu='{$menu["submenu"]}' AND language='".$this->langTranslation."'", "menuOrder ASC");
            }
            else if(!empty($menu["menu"]))
            {
                $contents = $this->getTable("Content")->fetchList(false, "menu='{$menu["menu"]}' AND language='".$this->langTranslation."'", "menuOrder ASC");
            }
            else
            {
                throw new \Exception("Oops, an error has occurred.");
            }
        }
        $this->setMetaTags($contents);
        $this->view->contents = $contents;
        return $this->view;
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
                }
                else
                {
                    $this->cache->error = $this->translation->CONTACT_ERROR;
                }
                return $this->redirect()->toUrl("/contact");
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
                return $this->redirect()->toUrl("/contact");
            }
        }
        return $this->view;
    }
}

?>