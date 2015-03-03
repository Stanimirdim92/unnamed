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
 * @category   Application\Module
 * @package    ZendPress
 * @author     Stanimir Dimitrov <stanimirdim92@gmail.com>
 * @copyright  2015 Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 * @version    0.03
 * @link       TBA
 */

namespace Application;

use Zend\Mvc\ModuleRouteListener;

// use Zend\Cache\StorageFactory;
// use Zend\Session\SaveHandler\Cache;
use Zend\Session\Config\SessionConfig;
use Zend\Session\Container;
use Zend\Session\SessionManager;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ModuleManager\Feature;
use Zend\Mvc\MvcEvent;

class Module implements Feature\AutoloaderProviderInterface,
                        Feature\ServiceProviderInterface,
                        Feature\ConfigProviderInterface
{
    /**
     * @param array $config Holds cookies params
     */
    public function initSession(array $config = array())
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
     * @param MvcEvent $e
     * @return Events
     */
    public function onBootstrap(MvcEvent $e)
    {
        /**
         * Init sessions and cookies before everything else
         */
        $this->initSession(array(
            'cookie_lifetime'     => 7200, //2hrs
            'remember_me_seconds' => 7200, //2hrs This is also set in the login controller
            'use_cookies'         => true,
            'cache_expire'        => 180,  //3hrs
            'cookie_path'         => "/",
            'cookie_secure'       => $this->isSSL(),
            'cookie_httponly'     => true,
            'name'                => '__zpc' // zend press cookie
        ));

        $app = $e->getTarget();
        $em = $app->getEventManager();
        $sm = $app->getServiceManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($em);

        $em->attach(MvcEvent::EVENT_RENDER, array($this, 'setLayoutTitle'));
        $em->attach(MvcEvent::EVENT_DISPATCH_ERROR, function (MvcEvent $e) use ($sm)
        {
            if (!$e->getParam("exception"))
            {
                $this->errorResponse($e);
            }
            else
            {
                $this->logError($sm->get('ApplicationErrorHandling'), $e->getParam("exception"), $e, $sm, "Guest");
            }
        });
    }

    /**
     * @param  ApplicationErrorHandling $service
     * @param  Exception $exception
     * @param  MvcEvent $e
     * @param  ServiceManager $sm
     *
     * @return void
     */
    private function logError($service, $exception, $e, $sm, $userRole = null)
    {
        if($exception instanceof \Custom\Error\AuthorizationException)
        {
            $cache = new Container("cache");
            $remote = new \Zend\Http\PhpEnvironment\RemoteAddress();
            if ($cache->role == 1)
            {
                $userRole = $cache->role;
            }
            else if ($cache->role == 10)
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
        $e->getViewModel()->setTemplate('layout/error-layout');
        $e->stopPropagation();
    }
    
    /**
     * @param \Zend\Mvc\MvcEvent $e
     */
    public function setLayoutTitle(MvcEvent $e)
    {
        $action = $e->getRouteMatch()->getParam('title');
        if (empty($action))
        {
            $action = $e->getRouteMatch()->getParam('action');
            if ($action === "index" && $e->getRouteMatch()->getMatchedRouteName() !== 'application')
            {
                $action = $e->getRouteMatch()->getMatchedRouteName();
            }
            else if ($action !== "index")
            {
                $action .= ($e->getRouteMatch()->getParam("post") ? " - ".$e->getRouteMatch()->getParam("post") : "");
            }
            else
            {
                $action = "Home"; // must be set from db
            }
        }
        $headTitleHelper = $e->getApplication()->getServiceManager()->get('ViewHelperManager')->get('headTitle');
        $headTitleHelper->append('ZendPress'); // must be set from db
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
                    $app = $helpers->getServiceLocator()->get('Application');
                    return new \Application\View\Helper\Params($app->getRequest(), $app->getMvcEvent());
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
                'ApplicationErrorHandling' => 'Application\Factory\ApplicationErrorHandlingFactory',
                'ResetPasswordTable'       => "Application\Factory\ResetPasswordTableFactory",
            ),
        );
    }
}
