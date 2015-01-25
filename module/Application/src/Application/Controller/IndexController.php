<?php
namespace Application\Controller;

use Zend\Cache\Storage\Adapter\Session;
use Zend\View\Model\ViewModel;
use Zend\Session\Container;

use Custom\Error\AuthorizationException;
use Custom\Plugins\Functions;
use Custom\Plugins\Mailing;

class IndexController extends \Zend\Mvc\Controller\AbstractActionController
{
    /**
     * @var null $cache holds any other session information, contains warning, success and error vars that are shown just once and then reset
     * @return Zend\Session\Container|mixed
     */
    protected $cache = null;

    /**
     * @var null $view creates instance to view model
     * @return Zend\View\Model\ViewModel
     */
    protected $view = null;

    /**
     * @var null $translation holds language data as well as all translations
     * @return Zend\Session\Container
     */
    protected $translation = null;
    protected $langTranslation = null;
    
    const NO_ID = 'no_id';

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
     * 
     * @param \Zend\Mvc\MvcEvent $e
     * @throws Exception\RuntimeException
     */
    public function onDispatch(\Zend\Mvc\MvcEvent $e)
    {
        parent::onDispatch($e);

        $this->initLanguages();
        $this->initViewVars();
        $this->initMenus();
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
        $this->view->language = $this->getTable("Language")->getLanguage($this->langTranslation);
        $this->view->controllerLong = $this->params('controller');
        $this->view->action = $this->params('action');
        $this->view->baseURL = $this->getRequest()->getUri()->getHost().$this->getRequest()->getRequestUri();
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
    }

    /**
     * initialize all menus for the front page
     */
    public function initMenus()
    {
        $temp = $this->getTable("Menu")->fetchList(false, array(), "(parent='0' AND menutype='0') AND language='".$this->langTranslation."'", "menuOrder ASC");
        $submenus = array();

        foreach($temp as $m)
        {
            $submenus[$m->id] = $this->getTable("Menu")->fetchList(false, array(), "(parent='" . (int) $m->id."' AND menutype='0') AND language='".$this->langTranslation."'", "menuOrder ASC");
        }
        $this->view->menus = $temp;
        $this->view->submenus = $submenus;
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
     * @throws AuthorizationException
     * @return void
     */
    protected function checkIdentity()
    {
        $auth = new \Zend\Authentication\AuthenticationService();
        if($auth->hasIdentity() && $this->cache->user instanceof \Admin\Model\User)
        {
            if( (($auth->getIdentity()->role === 1 || $auth->getIdentity()->role === 10) && $this->cache->logged) && 
                (($this->cache->role === 1 || $this->cache->role === 10) && $this->cache->logged))
            {
                return $this->redirect()->toUrl("/");
            }
            $this->clearUser();
        }
    }

    private function clearUser()
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
     * @param null $message holds the generated error(s)
     * @return string|array
     */
    protected function setErrorNoParam($message = null)
    {
        if(!empty($message))
        {
            $this->cache->error = $message;
        }
        else if ($message === static::NO_ID)
        {
            $this->cache->error = $this->translation->NO_ID_SET;
        }
        else
        {
            $this->cache->error = $this->translation->ERROR_STRING;
        }
        $this->view->setTerminal(true);
    }

    protected function setErrorCode($code = 404)
    {
        $this->getResponse()->setStatusCode($code);
        $this->view->setTemplate('layout/error-layout');
        return $this->view;
    }

// not done
    public function setMetaTags($obj = null, $page = true)
    {
        // $description = $keywords = $extract = $preview = $title = null;

        // if (empty($obj))
        // {
        //     return false;
        // }

        // /**
        //  * This section is called when we enter the pageAction and request a menu.
        //  */
        // if ($isPage === true)
        // {
        //     $description = $obj->current()->getMenuObject()->getDescription();
        //     $keywords = $obj->current()->getMenuObject()->getKeywords();
        //     $extract = $obj->current()->getExtract();
        //     $preview = $obj->current()->getPreview();
        //     $title = $obj->current()->getTitle();
        //     (empty($extract) ? $extract = $obj->current()->getText() : $extract);
        // }
        // /**
        //  * This section is called when we request newspost. 
        //  *
        //  * @see Application\Controller\NewsController
        //  */
        // else if ($isPage === "news")
        // {
        //     $extract = $obj->current()->getExtract();
        //     $preview = $obj->current()->getPreview();
        //     $title = $obj->current()->getTitle();
        //     (empty($extract) ? $extract = $obj->current()->getText() : $extract);
        // }
        // /**
        //  * All other pages? Maybe load the text from the database
        //  */
        // else
        // {
        //     $description = $obj->current()->getMenuObject()->getDescription();
        //     $keywords = $obj->current()->getMenuObject()->getKeywords();
        //     $extract = $obj->current()->getExtract();
        //     $preview = $obj->current()->getPreview();
        //     $title = $obj->current()->getTitle(); //cannot be empty
        // }
        
        // (empty($description) ? $description = "test desc" : $description);
        // (empty($keywords) ? $keywords = "test keyw" : $keywords);
        // (empty($preview) ? $preview = "" : $preview);

        // $hm = $this->getServiceLocator()->get('ViewHelperManager')->get('headMeta');
        // $placeholder = $this->getServiceLocator()->get('ViewHelperManager')->get('placeholder');
        // $placeholder->getContainer("customHead")->append("<meta itemprop='name' content='ZendPress'>\r\n");
        // // TODO: clear the new lines from the text. See the source code ctrl+u
        // $placeholder->getContainer("customHead")->append("<meta itemprop='description' content='".substr(strip_tags($extract), 0, 100)."..."."'>\r\n");
        // $placeholder->getContainer("customHead")->append("<meta itemprop='title' content='".$title."'>\r\n");
        // $placeholder->getContainer("customHead")->append("<meta itemprop='image' content='".$preview."'>\r\n");
        
        // $hm->appendName('keywords', $keywords);
        // $hm->appendName('description', $description);
        // $hm->appendProperty('og:image', $preview);
        // $hm->appendProperty("og:title", $title);
        // $hm->appendProperty("og:description", $description);
    }

/****************************************************
 * START OF ALL ACTION METHODS
 ****************************************************/

    public function indexAction()
    {
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
                if (!$result)
                {
                    $this->cache->error = $this->translation->CONTACT_ERROR;
                }
                $this->cache->success = $this->translation->CONTACT_SUCCESS;
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
                $this->setErrorNoParam($error);
                return $this->redirect()->toUrl("/contact");
            }
        }
        return $this->view;
    }
}

?>