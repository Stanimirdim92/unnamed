<?php
/**
 * MIT License.
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
 * @author     Stanimir Dimitrov <stanimirdim92@gmail.com>
 * @copyright  2015 (c) Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 * @version    0.0.13
 * @link       TBA
 */

namespace Application;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\EventManager\EventInterface;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\BootstrapListenerInterface;
use Zend\ModuleManager\Feature\InitProviderInterface;
use Zend\ModuleManager\ModuleManagerInterface;
use Zend\Session\Container;

class Module implements AutoloaderProviderInterface, ConfigProviderInterface, BootstrapListenerInterface, InitProviderInterface
{
    /**
     * Setup module layout
     *
     * @param  $moduleManager ModuleManager
     */
    public function init(ModuleManagerInterface $moduleManager)
    {
        $moduleManager->getEventManager()->getSharedManager()->attach(__NAMESPACE__, MvcEvent::EVENT_DISPATCH, function (MvcEvent $e) {
            $e->getTarget()->layout('layout/layout');
        });
    }

    /**
     * Listen to the bootstrap event
     *
     * @param EventInterface $e
     */
    public function onBootstrap(EventInterface $e)
    {
        $app = $e->getApplication();
        $moduleRouteListener = new ModuleRouteListener();
        $em = $app->getEventManager();
        $moduleRouteListener->attach($em);

        $sessionManager = $app->getServiceManager()->get('initSession');
        $sessionManager->setName("zpc")->start();
        Container::setDefaultManager($sessionManager);

        $em->attach(MvcEvent::EVENT_DISPATCH, [$this, 'onDispatch']);
        $em->attach(MvcEvent::EVENT_DISPATCH_ERROR, [$this, "onError"]);
    }

    /**
     * Log errors
     *
     * @param EventInterface $e
     * @return void
     */
    public function onError(EventInterface $e)
    {
        $sm = $e->getApplication()->getServiceManager();
        $service = $sm->get('ApplicationErrorHandling');
        $service->logError($e, $sm);
        $e->stopPropagation();
        return;
    }

    /**
     * Handle layout titles onDispatch
     *
     * @param EventInterface $e
     */
    public function onDispatch(EventInterface $e)
    {
        $app = $e->getApplication();
        $sm = $app->getServiceManager();
        $route = $e->getRouteMatch();
        $viewHelper = $sm->get('ViewHelperManager');
        $lang = new Container("translations");
        $translator = $sm->get('translator');

        /**
         * Get translations
         */
        $translator->setLocale($lang->languageName)->setFallbackLocale('en');
        $viewModel = $app->getMvcEvent()->getViewModel();
        $viewModel->lang = $translator->getLocale();

        /**
         * Setup website title
         */
        $action = $route->getParam('title');
        if (empty($action)) {
            $action = strtolower($route->getParam('action'));
            if ($action != "index") {
                $action = ($route->getParam("post") ?: "");
            }
        }

        $headTitleHelper = $viewHelper->get('headTitle');
        $headTitleHelper->append('Unnamed - '.ucfirst($action)); // must be set from db
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
                __DIR__.'/autoload_classmap.php',
            ],
            'Zend\Loader\StandardAutoloader' => [
                'namespaces' => [
                    __NAMESPACE__ => __DIR__.'/src/'.__NAMESPACE__,
                ],
            ],
        ];
    }
}
