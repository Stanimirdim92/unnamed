<?php

/**
 * @copyright  2015 (c) Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 *
 * @version    0.0.18
 *
 * @link       TBA
 */

namespace Application;

use Zend\Mvc\ModuleRouteListener;
use Zend\EventManager\EventInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\BootstrapListenerInterface;
use Zend\ModuleManager\Feature\InitProviderInterface;
use Zend\ModuleManager\ModuleManagerInterface;
use Zend\Session\Container;

final class Module implements ConfigProviderInterface, BootstrapListenerInterface, InitProviderInterface
{
    /**
     * Setup module layout.
     *
     * @param $moduleManager ModuleManager
     */
    public function init(ModuleManagerInterface $moduleManager)
    {
        $moduleManager->getEventManager()->getSharedManager()->attach(
            __NAMESPACE__,
            "dispatch",
            function (EventInterface $event) {
                $event->getTarget()->layout('layout/layout');
            }
        );
    }

    /**
     * Listen to the bootstrap event.
     *
     * @param EventInterface $event
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

        $eventManager->attach("dispatch", [$this, 'setTitleAndTranslation'], -10);
        $eventManager->attach("dispatch.error", [$this, "onError"], 2);
    }

    /**
     * Log errors.
     *
     * @param EventInterface $event
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
     * @param EventInterface $event
     */
    public function setTitleAndTranslation(EventInterface $event)
    {
        $app = $event->getApplication();
        $serviceManager = $app->getServiceManager();
        $route = $event->getRouteMatch();
        $title = $serviceManager->get('ControllerPluginManager')->get("systemsettings");
        $viewHelper = $serviceManager->get('ViewHelperManager');
        $lang = new Container("translations");
        $translator = $serviceManager->get('translator');

        /*
         * Load translations.
         */
        $translator->setLocale($lang->languageName)->setFallbackLocale('en');
        $viewModel = $app->getMvcEvent()->getViewModel();
        $viewModel->lang = $translator->getLocale();

        /*
         * Load page title
         */
        $action = ($route->getParam('post') ? ' - '.$route->getParam('post') : ucfirst($route->getParam('__CONTROLLER__')));

        $headTitleHelper = $viewHelper->get('headTitle');
        $headTitleHelper->append($title->__invoke('general', 'site_name')." ".$action);
    }

    /**
     * @return array|\Traversable
     */
    public function getConfig()
    {
        return include __DIR__.'/config/module.config.php';
    }
}
