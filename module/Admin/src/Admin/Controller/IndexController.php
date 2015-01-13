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
     * @param null $translation holds language data as well as all terms
     * @return Int|String
     */
    public $translation = null;
    
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
        $this->translation = new Container("translations");
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
        if(!class_exists("Admin\Model\Language"))
        {
            throw new Exception\RuntimeException($this->translation->LANGUAGE_CLASS_NOT_FOUND);
        }
        
        $this->initLanguages();
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
        $this->view->session = $this->translation;

        // all active languages
        $this->view->languages = $this->getTable("Language")->fetchList(false, "active='1'", "name ASC");

        // current language
        $this->view->languageObject = $this->getTable("Language")->getLanguage($this->translation->language);

        // current language id
        $this->view->language = $this->translation->language;
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
        $this->translation = new Container('translations');

        if($this->translation->language == null)
        {
            $this->translation->language = 1;
            $this->translation = Functions::initTranslations($this->translation->language, true);
        }
        else
        {
            $this->view->language = $this->getTable("Language")->getLanguage($this->translation->language);
            $this->translation = Functions::initTranslations($this->translation->language, false);
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
                        unset($checkAdminExistence);
                        $this->clearUser();
                    }
                }
                else
                {
                    unset($checkAdminExistence);
                    $this->clearUser();
                }
            }
            $this->clearUser();
        }
    }

    private function clearUser()
    {
        $this->cache->getManager()->getStorage()->clear();
        $this->translation->getManager()->getStorage()->clear();
        $this->cache = new Container("cache");
        $this->translation = new Container("translations");
        $authSession = new Container('ul');
        $authSession->getManager()->getStorage()->clear();
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
}
?>