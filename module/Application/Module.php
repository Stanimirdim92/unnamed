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
 * @package    Unnamed
 * @author     Stanimir Dimitrov <stanimirdim92@gmail.com>
 * @copyright  2015 Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 * @version    0.0.3
 * @link       TBA
 */

namespace Application;

use Zend\Mvc\ModuleRouteListener;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\BootstrapListenerInterface;
use Zend\Mvc\MvcEvent;
use Zend\EventManager\EventInterface;

class Module implements AutoloaderProviderInterface, ConfigProviderInterface, BootstrapListenerInterface
{

    /**
     * Listen to the bootstrap event
     *
     * @param EventInterface $e
     * @return array
     */
    public function onBootstrap(EventInterface $e)
    {

        $em = $e->getApplication()->getEventManager();
        $sm = $e->getTarget()->getServiceManager();

        $em->attach(MvcEvent::EVENT_DISPATCH, [$this, 'onDispatch']);
        $em->attach(MvcEvent::EVENT_DISPATCH_ERROR, function (MvcEvent $event) use ($sm) {
            $service = $sm->get('ApplicationErrorHandling');
            $service->logError($event, $sm);
        });

        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($em);
    }

    /**
     * Handle layout titles onDispatch
     *
     * @param Zend\Mvc\MvcEvent $e
     */
    public function onDispatch(MvcEvent $e)
    {
        /**
         * Setup module layouts
         */
        $moduleNamespace = substr(get_class($e->getTarget()), 0, strpos(get_class($e->getTarget()), '\\'));
        $config = $e->getApplication()->getServiceManager()->get('Config');
        $route = $e->getRouteMatch();
        if (isset($config['module_layouts'][$moduleNamespace])) {
            $e->getTarget()->layout($config['module_layouts'][$moduleNamespace]);
        }

        /**
         * Title param comes from MenuController
         */
        $action = trim($route->getParam('title'));

        if (empty($action)) {
            $action = strtolower($route->getParam('action'));
            if ($action !== "index") {
                /**
                 * Post param means that the user is in NewsController
                 */
                $action .= ($route->getParam("post") ? " - ".$route->getParam("post") : "");
            } else {
                /**
                 * Front main page aka IndexController::indexAction
                 */
                $action = "Home"; // must be set from db
            }
        }

        $headTitleHelper = $e->getApplication()->getServiceManager()->get('ViewHelperManager')->get('headTitle');
        $headTitleHelper->append('Unnamed'); // must be set from db
        $headTitleHelper->setSeparator(' - ');
        $headTitleHelper->append($action);
    }

    /**
     * @return array|\Traversable
     */
    public function getConfig()
    {
        return include __DIR__.'/config/module.config.php';
    }

    /**
     * Return an array for passing to Zend\Loader\AutoloaderFactory.
     *
     * @return array
     */
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
