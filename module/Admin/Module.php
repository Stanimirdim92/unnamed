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
 * @category   Admin\Module
 * @package    ZendPress
 * @author     Stanimir Dimitrov <stanimirdim92@gmail.com>
 * @copyright  2015 Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 * @version    0.03
 * @link       TBA
 */

namespace Admin;

use Admin\Model\Term;
use Admin\Model\TermTable;
use Admin\Model\TermCategory;
use Admin\Model\TermCategoryTable;
use Admin\Model\TermTranslation;
use Admin\Model\TermTranslationTable;
use Admin\Model\Language;
use Admin\Model\LanguageTable;
use Admin\Model\User;
use Admin\Model\UserTable;
use Admin\Model\AdminMenu;
use Admin\Model\AdminMenuTable;
use Admin\Model\Administrator;
use Admin\Model\AdministratorTable;
use Admin\Model\Content;
use Admin\Model\ContentTable;
use Admin\Model\Menu;
use Admin\Model\MenuTable;

use Admin\Controller\ErrorHandling as ErrorHandlingService;
use Admin\View\Helper;

use Zend\Session\Container;

use Zend\Log\Logger;
use Zend\Log\Writer\Stream as LogWriterStream;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;

use Zend\ServiceManager\ServiceLocatorInterface;

use Zend\ModuleManager\Feature;
use Zend\Mvc\MvcEvent;
use Zend\EventManager\EventInterface;

use Custom\Error\AuthorizationException;

class Module implements Feature\AutoloaderProviderInterface,
                        Feature\ServiceProviderInterface,
                        Feature\ConfigProviderInterface,
                        Feature\BootstrapListenerInterface
{
    /**
     * make sure to log errors and redirect to admin index
     * @param \Zend\Mvc\MvcEvent $e
     */
    public function onBootstrap(EventInterface $e)
    {
        $app = $e->getTarget();
        $em = $app->getEventManager();
        $sm = $app->getServiceManager();

        $em->attach(MvcEvent::EVENT_DISPATCH_ERROR, function (MvcEvent $e) use ($sm)
        {
            if (!$e->getParam("exception"))
            {
                return $this->errorResponse($e);
            }
            else
            {
                return $this->logError($sm->get('ApplicationErrorHandling'), $e->getParam("exception"), $e, $sm, "Guest");
            }
        });
    }

    /**
     * @param  ApplicationErrorHandling $service
     * @param  Exception $exception
     * @param  MvcEvent $e
     * @param  ServiceManager $sm
     *
     * @return [type]            
     */
    private function logError($service, $exception, $e, $sm, $userRole = null)
    {
        if(get_class($exception) === "Custom\Error\AuthorizationException")
        {
            $cache = new Container("cache");
            $remote = new \Zend\Http\PhpEnvironment\RemoteAddress();
            if ($cache->role === 1)
            {
                $userRole = $cache->role;
            }
            else if ($cache->role === 10)
            {
                $userRole = $cache->role;
            }
            $message = " *** APPLICATION LOG ***
            Controller: " . $e->getRouteMatch()->getParam('controller') . ",
            Controller action: " . $e->getRouteMatch()->getParam('action') . ",
            User role: " . $userRole. ",
            User id: " . (isset($cache->user->id) ? $cache->user->id : "Guest"). ",
            Admin: " . (isset($cache->user->admin) ? "Yes" : "No"). ",
            IP: " . $remote->getIpAddress() . ",
            Browser string: " . $_SERVER['HTTP_USER_AGENT'] . ",
            Date: " . date("Y-m-d H:i:s", time()) . ",
            Full URL: ".$sm->get("Request")->getRequestUri().",
            User port: ".$_SERVER["REMOTE_PORT"].",
            Remote host addr: ".gethostbyaddr($remote->getIpAddress()).",
            Method used: " . $sm->get("Request")->getMethod() . "\n";
            $service->logAuthorisationError($message);
        }
        else
        {
            $service->logException($exception);
        }
        $this->errorResponse($e);
    }

    /**
     * @param \Zend\Mvc\MvcEvent $e
     */
    private function errorResponse(MvcEvent $e)
    {
        $e->getResponse()->setStatusCode(404);
        $e->getResponse()->sendHeaders();
        $e->setResult($e->getResponse());
        $e->getViewModel()->setTemplate('layout/error-layout');
        $e->stopPropagation();
    }

    public function getConfig()
    {
        $config = include __DIR__ . '/config/module.config.php';
        $config['controllers'] = array(
            'invokables' => array(
                'Admin\Controller\Index'            => 'Admin\Controller\IndexController',
                'Admin\Controller\AdminMenu'        => 'Admin\Controller\AdminMenuController',
                'Admin\Controller\Language'         => 'Admin\Controller\LanguageController',
                'Admin\Controller\Term'             => 'Admin\Controller\TermController',
                'Admin\Controller\TermCategory'     => 'Admin\Controller\TermCategoryController',
                'Admin\Controller\TermTranslation'  => 'Admin\Controller\TermTranslationController',
                'Admin\Controller\User'             => 'Admin\Controller\UserController',
                'Admin\Controller\Administrator'    => 'Admin\Controller\AdministratorController',
                'Admin\Controller\Menu'             => 'Admin\Controller\MenuController',
                'Admin\Controller\Content'          => 'Admin\Controller\ContentController', 
            ),
        );
        return $config;
    }
    
    public function getViewHelperConfig()
    {
        return array(
            'factories' => array(
                'Params' => function (ServiceLocatorInterface $helpers)
                {
                    $app = $helpers->getServiceLocator()->get('Application');
                    return new Helper\Params($app->getRequest(), $app->getMvcEvent());
                }
            ),
        );
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__
                ),
            ),
        );
    }

    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'AdminErrorHandling' =>  function($sm)
                {
                    $logger = $sm->get('Logger');
                    $service = new ErrorHandlingService($logger);
                    return $service;
                },
                'Logger' => function ($sm)
                {
                    $filename = 'log_' . date('F') . '.txt';
                    $log = new Logger();
                    $writer = new LogWriterStream('./data/logs/' . $filename);
                    $log->addWriter($writer);
                    return $log;
                },

                'AdminMenuTable' => function ($sm)
                {
                    return new AdminMenuTable($sm);
                },
                'AdminMenuTableGateway' => function ($sm)
                {
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new AdminMenu(null, $sm));
                    return new TableGateway('adminmenu', $sm->get('Zend\Db\Adapter\Adapter'), null, $resultSetPrototype);
                },

                'TermTable' => function ($sm)
                {
                    return new TermTable($sm);
                },
                'TermTableGateway' => function ($sm)
                {
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Term());
                    return new TableGateway('term', $sm->get('Zend\Db\Adapter\Adapter'), null, $resultSetPrototype);
                },

                'TermCategoryTable' => function ($sm)
                {
                    return new TermCategoryTable($sm);
                },
                'TermCategoryTableGateway' => function ($sm)
                {
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new TermCategory());
                    return new TableGateway('termcategory', $sm->get('Zend\Db\Adapter\Adapter'), null, $resultSetPrototype);
                },

                'TermTranslationTable' => function ($sm)
                {
                    return new TermTranslationTable($sm);
                },
                'TermTranslationTableGateway' => function ($sm)
                {
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new TermTranslation());
                    return new TableGateway('termtranslation', $sm->get('Zend\Db\Adapter\Adapter'), null, $resultSetPrototype);
                },

                'LanguageTable' => function ($sm)
                {
                    return new LanguageTable($sm);
                },
                'LanguageTableGateway' => function ($sm)
                {
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Language(null, $sm));
                    return new TableGateway('language', $sm->get('Zend\Db\Adapter\Adapter'), null, $resultSetPrototype);
                },

                'UserTable' => function ($sm)
                {
                    return new UserTable($sm);
                },
                'UserTableGateway' => function ($sm)
                {
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new User(null, $sm));
                    return new TableGateway('user', $sm->get('Zend\Db\Adapter\Adapter'), null, $resultSetPrototype);
                },

                'UserClassTable' => function ($sm)
                {
                    return new UserClassTable($sm);
                },
                'UserClassTableGateway' => function ($sm)
                {
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new UserClass(null, $sm));
                    return new TableGateway('userclass', $sm->get('Zend\Db\Adapter\Adapter'), null, $resultSetPrototype);
                },

                'AdministratorTable' => function ($sm)
                {
                    return new AdministratorTable($sm);
                },
                'AdministratorTableGateway' => function ($sm)
                {
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Administrator(null, $sm));
                    return new TableGateway('administrator', $sm->get('Zend\Db\Adapter\Adapter'), null, $resultSetPrototype);
                },

                'ContentTable' => function ($sm)
                {
                    return new ContentTable($sm);
                },
                'ContentTableGateway' => function ($sm)
                {
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Content(array(), $sm));
                    return new TableGateway('content', $sm->get('Zend\Db\Adapter\Adapter'), null, $resultSetPrototype);
                },

                'MenuTable' => function ($sm)
                {
                    return new MenuTable($sm);
                },
                'MenuTableGateway' => function ($sm)
                {
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Menu(array(), $sm));
                    return new TableGateway('menu', $sm->get('Zend\Db\Adapter\Adapter'), null, $resultSetPrototype);
                },
            ),
        );
    }
}
