<?php

/**
 * @copyright  2015 (c) Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 * @version    0.0.17
 * @link       TBA
 */

namespace Application;

use Zend\Mvc\ModuleRouteListener;
use Zend\EventManager\EventInterface;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\BootstrapListenerInterface;
use Zend\ModuleManager\Feature\InitProviderInterface;
use Zend\ModuleManager\ModuleManagerInterface;
use Zend\Session\Container;

final class Module implements AutoloaderProviderInterface, ConfigProviderInterface, BootstrapListenerInterface, InitProviderInterface
{
    /**
     * Setup module layout.
     *
     * @param $moduleManager ModuleManager
     */
    public function init(ModuleManagerInterface $moduleManager)
    {
        $moduleManager->getEventManager()->getSharedManager()->attach(
            __NAMESPACE__, "dispatch", function (EventInterface $e) {
            $e->getTarget()->layout('layout/layout');
            }
        );
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
        $sm = $app->getServiceManager();
        $moduleRouteListener->attach($em);

        $sessionManager = $sm->get('initSession');
        $sessionManager->setName("zpc")->start();
        Container::setDefaultManager($sessionManager);

        $em->attach("dispatch", [$this, 'onDispatch'], -10);
        $em->attach("dispatch.error", [$this, "onError"], 2);
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

        $translator->setLocale($lang->languageName)->setFallbackLocale('en');
        $viewModel = $app->getMvcEvent()->getViewModel();
        $viewModel->lang = $translator->getLocale();

        $action = ($route->getParam('post') ? ' - '.$route->getParam('post') : ucfirst($route->getParam('__CONTROLLER__')));

        $headTitleHelper = $viewHelper->get('headTitle');
        $headTitleHelper->append($title->__invoke('general', 'site_name').$action);
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

    /**
     * @return array|\Traversable
     */
    public function getConfig()
    {
        return include __DIR__.'/config/module.config.php';
    }
}
