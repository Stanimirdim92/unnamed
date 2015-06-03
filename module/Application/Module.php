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
use Zend\Cache\StorageFactory;
use Zend\Session\SaveHandler\Cache;
use Zend\Session\Config\SessionConfig;
use Zend\Session\SessionManager;
use Zend\ModuleManager\Feature;
use Zend\Session\Container;
use Zend\Mvc\MvcEvent;
use Zend\EventManager\EventInterface;
use Custom\Plugins\Functions;

class Module implements
    Feature\AutoloaderProviderInterface,
    Feature\ConfigProviderInterface,
    Feature\BootstrapListenerInterface
{
    /**
     * @param array $config Holds cookies params
     */
    public function initSession()
    {
        $sessionConfig = new SessionConfig();
        $sessionConfig->setOptions([
            'cookie_lifetime'     => 7200, //2hrs
            'remember_me_seconds' => 7200, //2hrs This is also set in the login controller
            'use_cookies'         => true,
            'cache_expire'        => 180,  //3hrs
            'cookie_path'         => "/",
            'cookie_secure'       => Functions::isSSL(),
            'cookie_httponly'     => true,
            'name'                => '__zpc', // zend press cookie
        ]);
        $sessionManager = new SessionManager($sessionConfig);
        // $memCached = new StorageFactory::factory(array(
        //     'adapter' => array(
        //        'name'     =>'memcached',
        //         'lifetime' => 7200,
        //         'options'  => array(
        //             'servers'   => array(
        //                 array(
        //                     '127.0.0.1',11211
        //                 ),
        //             ),
        //             'namespace'  => 'MYMEMCACHEDNAMESPACE',
        //             'liboptions' => array(
        //                 'COMPRESSION' => true,
        //                 'binary_protocol' => true,
        //                 'no_block' => true,
        //                 'connect_timeout' => 100
        //             )
        //         ),
        //     ),
        // ));

        // $saveHandler = new Cache($memCached);
        // $sessionManager->setSaveHandler($saveHandler);
        $sessionManager->start();
        $sessionManager->getValidatorChain()->attach('session.validate', [ new \Zend\Session\Validator\HttpUserAgent(), 'isValid']);
        $sessionManager->getValidatorChain()->attach('session.validate', [ new \Zend\Session\Validator\RemoteAddr(), 'isValid']);
        return Container::setDefaultManager($sessionManager);
    }

    /**
     * @param EventInterface $e
     */
    public function onBootstrap(EventInterface $e)
    {
        /**
         * Init sessions and cookies before everything else
         */
        $this->initSession();

        $em = $e->getApplication()->getEventManager();
        $sm = $e->getApplication()->getServiceManager();

        $em->attach(MvcEvent::EVENT_DISPATCH, [$this, 'setModuleLayouts']);
        $em->attach(MvcEvent::EVENT_DISPATCH, [$this, 'onDispatch']);
        $em->attach(MvcEvent::EVENT_DISPATCH_ERROR, function (MvcEvent $e) use ($sm) {
            return $this->logError($sm->get('ApplicationErrorHandling'), $e, $sm);
        });

        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($em);
    }

    /**
     * @param  ApplicationErrorHandling $service
     * @param  Zend\Mvc\MvcEvent $e
     * @param  ServiceManager $sm
     * @param  $userRole int
     *
     * @return void
     */
    private function logError($service, $e, $sm, $userRole = "Guest")
    {
        if ($e->getParam("exception") instanceof \Custom\Error\AuthorizationException) {
            $service->logAuthorisationError($e, $sm, new Container("cache"), $userRole);
        } elseif ($e->getParam("exception") != null) {
            $service->logException($e->getParam("exception"));
        } else {
            return $this->errorResponse($e);
        }
        return $this->errorResponse($e);
    }

    /**
     * This function is used to simulate a fake redirect to errors page,
     * where it will show a friendly error message to the user.
     * The error message comes from the throwed exception.
     * Also make sure that we almost always send a 404 response.
     *
     * @param Zend\Mvc\MvcEvent $e
     * @return  MvcEvent
     */
    private function errorResponse(MvcEvent $e)
    {
        $e->getResponse()->setStatusCode(404);
        $e->getViewModel()->setVariables([
            'message' => '404 Not found',
            'reason' => 'Error',
            'exception' => ($e->getParam("exception") ? $e->getParam("exception")->getMessage(): ""),
        ]);
        $e->getViewModel()->setTemplate('error/index.phtml');
        $e->stopPropagation();
        return $e;
    }

    /**
     * Handle layout titles onDispatch
     *
     * @param Zend\Mvc\MvcEvent $e
     */
    public function onDispatch(MvcEvent $e)
    {
        $action = $e->getRouteMatch()->getParam('title');

        if (empty($action)) {
            $action = strtolower($e->getRouteMatch()->getParam('action'));
            if ($action === "index" && $e->getRouteMatch()->getMatchedRouteName() !== 'application') {
                $action = $e->getRouteMatch()->getMatchedRouteName();
            } elseif ($action !== "index") {
                $action .= ($e->getRouteMatch()->getParam("post") ? " - ".$e->getRouteMatch()->getParam("post") : "");
            } else {
                $action = "Home"; // must be set from db
            }
        }

        $headTitleHelper = $e->getApplication()->getServiceManager()->get('ViewHelperManager')->get('headTitle');
        $headTitleHelper->append('ZendPress'); // must be set from db
        $headTitleHelper->setSeparator(' - ');
        $headTitleHelper->append($action);
    }

    /**
     * Handle different layouts for each module
     */
    public function setModuleLayouts(MvcEvent $e)
    {
        $moduleNamespace = substr(get_class($e->getTarget()), 0, strpos(get_class($e->getTarget()), '\\'));
        $config = $e->getApplication()->getServiceManager()->get('Config');
        if (isset($config['module_layouts'][$moduleNamespace])) {
            $e->getTarget()->layout($config['module_layouts'][$moduleNamespace]);
        }
    }

    /**
     * @return array|mixed|\Traversable
     */
    public function getConfig()
    {
        return include __DIR__.'/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return [
            'Zend\Loader\ClassMapAutoloader' => [
                __DIR__ . '/autoload_classmap.php',
            ],
            'Zend\Loader\StandardAutoloader' => [
                'namespaces' => [
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ],
            ],
        ];
    }
}
