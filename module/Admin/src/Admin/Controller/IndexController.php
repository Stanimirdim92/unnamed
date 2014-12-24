<?php
namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Cache\Storage\Adapter\Session;
use Zend\View\Model\ViewModel;
use Zend\Session\Container;

use Custom\Error\AuthorizationException;
use Custom\Plugins\Functions;

class IndexController extends AbstractActionController
{
    /**
     * @param null $cache holds any other session information, contains warning,success and error vars that are shown just once and then reset
     * @return unknown
     */
    public $cache = null;

    /**
     * @param null $view creates instance to view model
     * @return Zend\View\Model\ViewModel Zend View Model
     */
    public $view = null;

    /**
     * @param null $session holds language data as well as all terms
     * @return Int|String
     */
    public $session = null;
    
    /**
     * @param array $breadcrumbs returns an array with links with the current user position on the website
     * @return Array
     */
    public $breadcrumbs = array();

    /**
     * constructor
     */
    public function __construct()
    {
        $this->view = new ViewModel();
        $this->session = new Container("translations");
        $this->breadcrumbs[] = array("reference" => "/admin","name" => "Home");
        $this->initCache();
    }

    /**
     * Initialize any variables before controller actions
     *
     * @throws Exception\RuntimeException
     * @param MvcEvent $e
     */
    public function onDispatch(\Zend\Mvc\MvcEvent $e)
    {
        // $this->initAdminIdentity();
        parent::onDispatch($e);
        if(class_exists("Admin\Model\Language"))
        {
            $this->initLanguages();
        }
        else
        {
            throw new Exception\RuntimeException("Language class was not found");
        }

        $this->initViewVars();
        $this->initBreadcrumbs();
        $this->initMenus();
        
        $this->view->controller = strtolower(substr($this->params('controller'), strrpos($this->params('controller'),"\\")+1));
        $this->view->action = $this->params('action');
        return $this->view;
    }

    /**
     * @param String $name
     * @return Ambigous <object, multitype:>
     */
    public function getTable($name)
    {
        return $this->getServiceLocator()->get($name."Table");
    }

    /** 
     * initialize breadcrumbs (if necessary)
     */
    public function initBreadcrumbs()
    {
        $this->view->breadcrumbs = $this->breadcrumbs;
    }

    public function addBreadcrumb($breadcrumb)
    {
        $this->breadcrumbs[] = $breadcrumb;
    }

    /** 
     * initialize any session variables in this method
     */
    public function initCache()
    {
        $this->cache = new Container("cache");
        $this->view->cache = $this->cache;
    }

    /**
     * initialize any view related stuff
     */
    public function initViewVars()
    {
        // session object
        $this->view->session = $this->session;

        // all active languages
        $this->view->languages = $this->getTable("Language")->fetchList(false, "active='1'", "name ASC");

        // current language
        $this->view->languageObject = $this->getTable("Language")->getLanguage($this->session->language);

        // current language id
        $this->view->language = $this->session->language;
    }

    /** 
     * initialize the admin menus
     */
    public function initMenus()
    {
        $controller = strtolower(substr($this->params('controller'), strrpos($this->params('controller'),"\\")+1));
        $this->view->controller = $controller;
        $action =  $this->params('action');
        $this->view->action = $action;
        $this->view->adminMenus = $this->getTable("AdminMenu")->fetchList(false, "parent='0' AND advanced='0'", "menuOrder");
        $this->view->advancedMenus = $this->getTable("AdminMenu")->fetchList(false, "parent='0' AND advanced='1'", "menuOrder");
        $this->view->adminsubmenus = $this->getTable("AdminMenu")->fetchList(false, "parent !='0' AND controller='{$controller}'", "menuOrder");
        // get the currently selected menu
        $this->view->selected = $this->getSelectedMenu($controller, $action);
    }

    /** 
     * this function tries to retrieve the current menu based on controller and action
     * In theory it is impossible to have 2 menus with same set of controller and action
     */
    public function getSelectedMenu($controller, $action)
    {
        $temp = $this->getTable("AdminMenu")->fetchList(false, "controller='{$controller}' AND action='{$action}'", "parent DESC");
        if(sizeof($temp) > 0)
        {
            return $temp->current();
        }
        else
        {
            $temp = $this->getTable("AdminMenu")->fetchList(false, "controller='{$controller}'", "parent ASC");
            if(sizeof($temp) > 0) return $temp->current();
        }
        return null;
    }

    /** 
     * initialize languages and language-related stuff like translations.
     */
    public function initLanguages()
    {
        $this->session = new Container('translations');

        if($this->session->language == null)
        {
            $this->session->language = 1;
            $this->session = Functions::initTranslations($this->session->language, true);
        }
        else
        {
            $this->view->language = $this->getTable("Language")->getLanguage($this->session->language);
            $this->session = Functions::initTranslations($this->session->language, false);
        }
    }

    public function indexAction()
    {
        return $this->view;
    }

    // check admin identity before initialize anything else
    private function initAdminIdentity()
    {
        if (is_object($this->cache) && isset($this->cache) && $this->cache instanceof \Zend\Session\Container)
        {
            if(is_object($this->cache->user) && isset($this->cache->user) && $this->cache->user instanceof \Admin\Model\User)
            {
                if($this->cache->role == 10 && $this->cache->logged && $this->cache->user->admin == 1)
                {
                    $checkAdminExistence = $this->getTable("administrator")->fetchList(false, "user='{$this->cache->user->id}'");
                    if (sizeof($checkAdminExistence) == 1)
                    {
                        return true;
                    }
                    else
                    {
                        $checkAdminExistence = null;
                        throw new AuthorizationException($this->session->ERROR_AUTHORIZATION);
                    }
                }
                else
                {
                    throw new AuthorizationException($this->session->ERROR_AUTHORIZATION);
                }
            }
            else
            {
                $this->cache->getManager()->getStorage()->clear();
                $this->session->getManager()->getStorage()->clear();
                $this->cache = new Container("cache");
                $this->session = new Container("translations");
                $authSession = new Container('Zend_Auth');
                $authSession->getManager()->getStorage()->clear();
                throw new AuthorizationException($this->session->ERROR_AUTHORIZATION);
            }
        }
        else
        {
            $this->cache->getManager()->getStorage()->clear();
            $this->session->getManager()->getStorage()->clear();
            $this->cache = new Container("cache");
            $this->session = new Container("translations");
            $authSession = new Container('Zend_Auth');
            $authSession->getManager()->getStorage()->clear();
            throw new AuthorizationException($this->session->ERROR_AUTHORIZATION);
        }
    }

    /**
     * Shorthand method for getting params from URLs. Makes code easier to modify and avoids DRY code
     *
     * @param unknown $param
     * @return String|Int|Null|Object|Boolean
     */
    public function getParam($paramName, $default = null)
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
     * @param String $message
     * @return string
     */
    public function errorNoParam($message = '')
    {
        if($message != '')
        {
            $this->cache->error = $message;
        }
        else if ($message == 'no_id')
        {
            $this->cache->error = "No id was set";
        }
        else
        {
            $this->cache->error = "Error!";
        }
        $this->view->setTerminal(true);
    }
}
?>