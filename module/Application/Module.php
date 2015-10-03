<?php

/**
 * @copyright  2015 (c) Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 * @version    0.0.15
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
     * Setup module layout.
     *
     * @param $moduleManager ModuleManager
     */
    public function init(ModuleManagerInterface $moduleManager)
    {
        $moduleManager->getEventManager()->getSharedManager()->attach(__NAMESPACE__, MvcEvent::EVENT_DISPATCH, function (MvcEvent $e) {
            $e->getTarget()->layout('layout/layout');
        });
    }

    /**
     * Listen to the bootstrap event.
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
     * Log errors.
     *
     * @param EventInterface $e
     *
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
     * Handle layout titles onDispatch.
     *
     * @param EventInterface $e
     */
    public function onDispatch(EventInterface $e)
    {
        $app = $e->getApplication();
        $sm = $app->getServiceManager();
        $route = $e->getRouteMatch();
        $title = $sm->get('ControllerPluginManager')->get("systemsettings");
        $viewHelper = $sm->get('ViewHelperManager');
        $lang = new Container("translations");
        $translator = $sm->get('translator');

        /*
         * Get translations
         */
        $translator->setLocale($lang->languageName)->setFallbackLocale('en');
        $viewModel = $app->getMvcEvent()->getViewModel();
        $viewModel->lang = $translator->getLocale();

        /*
         * Setup website title
         */
        $action = $route->getParam('title');
        if (empty($action)) {
            $action = strtolower($route->getParam('action'));
            if ($action != 'index') {
                $action = ($route->getParam('post') ?: "");
            }
        }

        $headTitleHelper = $viewHelper->get('headTitle');
        $headTitleHelper->append($title->__invoke('general', 'site_name').' - '.ucfirst($action)); // must be set from db
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
