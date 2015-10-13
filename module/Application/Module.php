<?php

/**
 * @copyright  2015 (c) Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 * @version    0.0.18
 * @link       TBA
 */

namespace Application;

use Zend\Mvc\ModuleRouteListener;
use Zend\EventManager\EventInterface;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\BootstrapListenerInterface;
use Zend\ModuleManager\ModuleManagerInterface;
use Zend\Session\Container;

final class Module implements AutoloaderProviderInterface, ConfigProviderInterface, BootstrapListenerInterface
{
    /**
     * Listen to the bootstrap event.
     *
     * @param EventInterface $e
     */
    public function onBootstrap(EventInterface $event)
    {
        $app = $event->getApplication();
        $moduleRouteListener = new ModuleRouteListener();
        $eventManager = $app->getEventManager();
        $serviceManager = $app->getServiceManager();
        $moduleRouteListener->attach($eventManager);

        $sessionManager = $serviceManager->get('initSession');
        $sessionManager->setName("zpc")->start();
        Container::setDefaultManager($sessionManager);

        $eventManager->attach("render", [$this,'setTheme'], 100);
        $eventManager->attach("dispatch", [$this, 'setTitleAndTranslation'], -10);
        $eventManager->attach("dispatch.error", [$this, "onError"], 2);
    }

    /**
     * Setup theme
     *
     * @param EventInterface $e
     */
    public function setTheme(EventInterface $event)
    {
        return $event->getApplication()->getServiceManager()->get('initThemes');
    }

    /**
     * Log errors.
     *
     * @param EventInterface $e
     *
     * @return void
     */
    public function onError(EventInterface $event)
    {
        $serviceManager = $event->getApplication()->getServiceManager();
        $service = $serviceManager->get('ErrorHandling');
        $service->logError($event, $serviceManager);
        return $event->stopPropagation();
    }

    /**
     * Handle layout titles onDispatch.
     *
     * @param EventInterface $e
     */
    public function setTitleAndTranslation(EventInterface $e)
    {
        $app = $e->getApplication();
        $serviceManager = $app->getServiceManager();
        $route = $e->getRouteMatch();
        $title = $serviceManager->get('ControllerPluginManager')->get("systemsettings");
        $viewHelper = $serviceManager->get('ViewHelperManager');
        $lang = new Container("translations");
        $translator = $serviceManager->get('translator');

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
