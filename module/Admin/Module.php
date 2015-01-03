<?php
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

use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\Log\Logger;
use Zend\Session\Container;
use Zend\Log\Writer\Stream as LogWriterStream;
use Zend\ServiceManager\ServiceLocatorInterface;

use Zend\ModuleManager\Feature;

use Zend\EventManager\EventInterface;
use Zend\Mvc\MvcEvent;
use Zend\Http\Request as HttpRequest;
use Zend\Http\Response as HttpResponse;
use Zend\Http\PhpEnvironment\RemoteAddress;

use Custom\Error\AuthorizationException;

class Module implements Feature\AutoloaderProviderInterface,
                        Feature\ServiceProviderInterface,
                        Feature\ConfigProviderInterface,
                        Feature\BootstrapListenerInterface
{
    /**
     * make sure to log errors and redirect to admin index
     * @param Event $e
     */
    public function onBootstrap(EventInterface $e)
    {
        $application = $e->getTarget();
        $eventManager = $application->getEventManager();
        $services = $application->getServiceManager();

        $eventManager->attach(MvcEvent::EVENT_DISPATCH, function (MvcEvent $event) use ($services)
        {
            $request  = $event->getRequest();
            $response = $event->getResponse();

            // Not HTTP? Kill it before it lays eggs!
            if (!($request instanceof HttpRequest && $response instanceof HttpResponse))
            {
                $response->setStatusCode(500);
                $event->setResult($response);
                $response->sendHeaders();
                $event->stopPropagation();
            }

            if (strtolower($event->getRouteMatch()->getMatchedRouteName()) === "admin")
            {
                $authAdapter = $services->get('Admin\AuthenticationAdapter');
                $authAdapter->setRequest($request);
                $authAdapter->setResponse($response);
                $result = $authAdapter->authenticate();
                if ($result->isValid())
                {
                    $ok = false;
                    $identity = $result->getIdentity();
                    $file = file_get_contents($_SERVER["DOCUMENT_ROOT"].'../../config/autoload/real/basic_passwd.txt');
                    $lines = explode("\n", $file);

                    foreach ($lines as $value)
                    {
                        $str = explode(":", $value);
                        if ($identity["username"] == $str[0] && $identity["realm"] == "admin")
                        {
                            $ok = true;
                            break;
                        }
                    }
                    if ($ok)
                    {
                        return true;
                    }
                    else
                    {
                        $service = $services->get('AdminErrorHandling');
                        $service->logException("add msg");
                        $response->setStatusCode(HttpResponse::STATUS_CODE_401);
                        $event->setResult($response);
                        $response->sendHeaders();
                        $event->stopPropagation();
                    }
                }
            }
        });

        $eventManager->attach(MvcEvent::EVENT_DISPATCH_ERROR, function (MvcEvent $event) use ($services)
        {
            $route = strtolower($event->getRouteMatch()->getMatchedRouteName());
            if (!$event->getRouteMatch() || $route === "admin")
            {
                $exception = $event->getParam("exception");
                if (!$exception)
                {
                    $event->getResponse()->setStatusCode(HttpResponse::STATUS_CODE_404);
                    $viewModel = $event->getViewModel();
                    $viewModel->setTemplate('layout/error-layout');
                    $event->stopPropagation();
                }
                else
                {
                    $service = $services->get('AdminErrorHandling');
                    $controllerRedirect = $event->getTarget();
                    if(get_class($exception)=="Custom\Error\AuthorizationException")
                    {
                        $cache = new Container("cache");
                        $remote = new RemoteAddress();
                        $userRole = "Guest";
                        if ($cache->role === 1)
                        {
                            $userRole = 1;
                        }
                        else if ($cache->role === 10)
                        {
                            $userRole = 10;
                        }
                        $routeMatch = $event->getRouteMatch();
                        $controller = $routeMatch->getParam('controller');
                        $action = $routeMatch->getParam('action');
                        $message = " *** ADMIN LOG ***
                        Controller: " . $controller . ",
                        Controller action: " . $action . ",
                        User role: " . $userRole. ",
                        User id: " . isset($cache->user->id) ? $cache->user->id : "Guest". ",
                        Admin: " . (isset($cache->user->admin) ? "Yes" : "No"). ",
                        IP: " . $remote->getIpAddress() . ",
                        Browser string: " . $_SERVER['HTTP_USER_AGENT'] . ",
                        Date: " . date("Y-m-d H:i:s", time()) . ",
                        Full URL: ".$_SERVER["REQUEST_URI"].",
                        User port: ".$_SERVER["REMOTE_PORT"].",
                        Remote host addr: ".gethostbyaddr($remote->getIpAddress()).",
                        Method used: " . $_SERVER['REQUEST_METHOD'] . "\n";
                        $service->logAuthorisationError($message);
                        $controllerRedirect->plugin('redirect')->toUrl("/");
                    }
                    else
                    {
                        $service->logException($exception);
                        $event->getResponse()->setStatusCode(HttpResponse::STATUS_CODE_404);
                        $viewModel = $event->getViewModel();
                        $viewModel->setTemplate('layout/error-layout');
                        $event->stopPropagation();
                    }
                }
            }
        });
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
                    $services = $helpers->getServiceLocator();
                    $app = $services->get('Application');
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
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new AdminMenu(null, $sm));
                    return new TableGateway('adminmenu', $dbAdapter, null, $resultSetPrototype);
                },

                'TermTable' => function ($sm)
                {
                    return new TermTable($sm);
                },
                'TermTableGateway' => function ($sm)
                {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Term());
                    return new TableGateway('term', $dbAdapter, null, $resultSetPrototype);
                },

                'TermCategoryTable' => function ($sm)
                {
                    return new TermCategoryTable($sm);
                },
                'TermCategoryTableGateway' => function ($sm)
                {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new TermCategory());
                    return new TableGateway('termcategory', $dbAdapter, null, $resultSetPrototype);
                },

                'TermTranslationTable' => function ($sm)
                {
                    return new TermTranslationTable($sm);
                },
                'TermTranslationTableGateway' => function ($sm)
                {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new TermTranslation());
                    return new TableGateway('termtranslation', $dbAdapter, null, $resultSetPrototype);
                },

                'LanguageTable' => function ($sm)
                {
                    return new LanguageTable($sm);
                },
                'LanguageTableGateway' => function ($sm)
                {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Language(null, $sm));
                    return new TableGateway('language', $dbAdapter, null, $resultSetPrototype);
                },

                'UserTable' => function ($sm)
                {
                    return new UserTable($sm);
                },
                'UserTableGateway' => function ($sm)
                {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new User(null, $sm));
                    return new TableGateway('user', $dbAdapter, null, $resultSetPrototype);
                },

                'UserClassTable' => function ($sm)
                {
                    return new UserClassTable($sm);
                },
                'UserClassTableGateway' => function ($sm)
                {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new UserClass(null, $sm));
                    return new TableGateway('userclass', $dbAdapter, null, $resultSetPrototype);
                },

                'AdministratorTable' => function ($sm)
                {
                    return new AdministratorTable($sm);
                },
                'AdministratorTableGateway' => function ($sm)
                {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Administrator(null, $sm));
                    return new TableGateway('administrator', $dbAdapter, null, $resultSetPrototype);
                },

                'ContentTable' => function ($sm)
                {
                    return new ContentTable($sm);
                },
                'ContentTableGateway' => function ($sm)
                {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Content(null, $sm));
                    return new TableGateway('content', $dbAdapter, null, $resultSetPrototype);
                },

                'MenuTable' => function ($sm)
                {
                    return new MenuTable($sm);
                },
                'MenuTableGateway' => function ($sm)
                {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Menu(null, $sm));
                    return new TableGateway('menu', $dbAdapter, null, $resultSetPrototype);
                },
            ),
        );
    }
}
