<?php
namespace Application;

use Zend\Mvc\ModuleRouteListener;

use Zend\Log\Logger;
use Zend\Log\Writer\Stream as LogWriterStream;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;

// use Zend\Cache\StorageFactory;
// use Zend\Session\SaveHandler\Cache;
use Zend\Session\Config\SessionConfig;
use Zend\Session\Container;
use Zend\Session\SessionManager;

use Zend\ServiceManager\ServiceLocatorInterface;

use Zend\ModuleManager\Feature;
use Zend\Mvc\MvcEvent;
use Zend\Http\Request as HttpRequest;
use Zend\Http\Response as HttpResponse;
use Zend\Http\PhpEnvironment\RemoteAddress;
use Zend\EventManager\EventInterface;

use Application\View\Helper;
use Application\Controller\ErrorHandling as ErrorHandlingService;
use Application\Model\ResetPassword;
use Application\Model\ResetPasswordTable;

class Module implements Feature\AutoloaderProviderInterface,
                        Feature\ServiceProviderInterface,
                        Feature\ConfigProviderInterface,
                        Feature\BootstrapListenerInterface
{
    /**
     * @param array $config Holds cookies params
     */
    public function initSession(array $config)
    {
        $sessionConfig = new SessionConfig();
        $sessionConfig->setOptions($config);
        $sessionManager = new SessionManager($sessionConfig);
        // $memCached = StorageFactory::factory(array(
        //     'adapter' => array(
        //        'name' => 'memcached',
        //        'options' => array(
        //            'server' => 'zend.localhost',
        //        ),
        //     ),
        // ));
        // $saveHandler = new Cache($memCached);
        // $sessionManager->setSaveHandler($saveHandler);
        $sessionManager->start();
        Container::setDefaultManager($sessionManager);
    }

    /**
     * Detect SSL/TLS protocol. If true activate cookie_secure key
     *
     * @return bool
     */
    private function isSSL()
    {
        if (isset($_SERVER['HTTPS']))
        {
            if ('on' == strtolower($_SERVER['HTTPS']) || '1' == $_SERVER['HTTPS'])
            {
                return true;
            }
        } 
        else if (isset($_SERVER['SERVER_PORT']) && ('443' == $_SERVER['SERVER_PORT']))
        {
            return true;
        }
        return false;
    }

    /**
     * make sure to log errors and redirect to error-layout
     * @param \Zend\Mvc\MvcEvent $e
     */
    public function onBootstrap(EventInterface $e)
    {
        /**
         * Init sessions and cookies before everything else
         */
        $this->initSession(array(
            'cookie_lifetime'     => 7200, //2hrs
            'remember_me_seconds' => 7200, //2hrs This is also set in the login controller
            'use_cookies'         => true,
            'cache_expire'        => 180,  //2hrs
            'cookie_path'         => "/",
            'cookie_secure'       => $this->isSSL(),
            'cookie_httponly'     => true,
            'name'                => '__zpc' // zend press cookie
        ));

        $app = $e->getTarget();
        $em = $app->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($em);
        $sm = $app->getServiceManager();
        $req = $sm->get("Request");

        $em->attach(MvcEvent::EVENT_DISPATCH_ERROR, function (MvcEvent $e) use ($sm)
        {
            $request  = $e->getRequest();
            $response = $e->getResponse();

            // Not HTTP? Kill it before it lays eggs!
            if (!($request instanceof HttpRequest && $response instanceof HttpResponse))
            {
                $response->setStatusCode(500);
                $e->setResult($response);
                $response->sendHeaders();
                $e->stopPropagation();
            }

            if(!$e->getRouteMatch() || strtolower($e->getRouteMatch()->getMatchedRouteName()) === "application")
            {
                $exception = $e->getParam("exception");
                if (!$exception)
                {
                    $response->setStatusCode(404);
                    $e->setResult($response);
                    $response->sendHeaders();
                    $viewModel = $e->getViewModel();
                    $viewModel->setTemplate('layout/error-layout');
                }
                else
                {
                    $cache = new Container("cache");
                    $remote = new RemoteAddress();
                    $service = $sm->get('ApplicationErrorHandling');
                    if(get_class($exception)==="Custom\Error\AuthorizationException")
                    {
                        $userRole = "Guest";
                        if ($cache->role === 1)
                        {
                            $userRole = 1;
                        }
                        else if ($cache->role === 10)
                        {
                            $userRole = 10;
                        }
                        $routeMatch = $e->getRouteMatch();
                        $controller = $routeMatch->getParam('controller');
                        $action = $routeMatch->getParam('action');
                        $message = " *** APPLICATION LOG ***
                        Controller: " . $controller . ",
                        Controller action: " . $action . ",
                        User role: " . $userRole. ",
                        User id: " . (isset($cache->user->id) ? $cache->user->id : "Guest"). ",
                        Admin: " . (isset($cache->user->admin) ? "Yes" : "No"). ",
                        IP: " . $remote->getIpAddress() . ",
                        Browser string: " . $_SERVER['HTTP_USER_AGENT'] . ",
                        Date: " . date("Y-m-d H:i:s", time()) . ",
                        Full URL: ".$req->getRequestUri().",
                        User port: ".$_SERVER["REMOTE_PORT"].",
                        Remote host addr: ".gethostbyaddr($remote->getIpAddress()).",
                        Method used: " . $req->getMethod() . "\n";
                        $service->logAuthorisationError($message);
                    }
                    else
                    {
                        $service->logException($exception);
                    }
                    $response->setStatusCode(404);
                    $e->setResult($response);
                    $response->sendHeaders();
                    $viewModel = $e->getViewModel();
                    $viewModel->setTemplate('layout/error-layout');
                }
            }
        });
        $em->attach(MvcEvent::EVENT_DISPATCH, array($this, 'setLayoutTitle'));
    }
    
    /**
     * @param \Zend\Mvc\MvcEvent $e
     */
    public function setLayoutTitle(MvcEvent $e)
    {
        $matches = $e->getRouteMatch();
        $action = $matches->getParam('title');

        if (empty($action))
        {
            $action = $matches->getParam('action');
            if ($action === "index" && $matches->getMatchedRouteName() !== 'application')
            {
                $action = $matches->getMatchedRouteName();
            }
            else if ($action !== "index")
            {
                $action .= ($matches->getParam("post") ? " - ".$matches->getParam("post") : "");
            }
            else
            {       
                $action = "Home"; // must be set from db
            }
        }

        $siteName = 'ZendPress'; // must be set from db
        $headTitleHelper = $e->getApplication()->getServiceManager()->get('ViewHelperManager')->get('headTitle');
        $headTitleHelper->append($siteName);
        $headTitleHelper->setSeparator(' - ');
        $headTitleHelper->append(ucfirst($action));
    }

    public function getConfig()
    {
        $config = include __DIR__ . '/config/module.config.php';
        $config['controllers'] = array(
            'invokables' => array(
                'Application\Controller\Index'        => 'Application\Controller\IndexController',
                'Application\Controller\Login'        => 'Application\Controller\LoginController',
                'Application\Controller\Registration' => 'Application\Controller\RegistrationController',
                'Application\Controller\Profile'      => 'Application\Controller\ProfileController',
                'Application\Controller\News'         => 'Application\Controller\NewsController',
                'Application\Controller\Menu'         => 'Application\Controller\MenuController',
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
                    $sl = $helpers->getServiceLocator();
                    $app = $sl->get('Application');
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
                'ApplicationErrorHandling' =>  function ($sm)
                {
                    $logger = $sm->get('Logger');
                    $service = new ErrorHandlingService($logger);
                    return $service;
                },
                'Logger' => function ($sm)
                {
                    $filename = 'front_end_log_' . date('F') . '.txt';
                    $log = new Logger();
                    $writer = new LogWriterStream('./data/logs/' . $filename);
                    $log->addWriter($writer);
                    return $log;
                },

                'ResetPasswordTable' => function ($sm)
                {
                    $table = new ResetPasswordTable($sm);
                    return $table;
                },
                'ResetPasswordTableGateway' => function ($sm)
                {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new ResetPassword(null, $sm));
                    return new TableGateway('resetpassword', $dbAdapter, null, $resultSetPrototype);
                },
            ),
        );
    }
}
