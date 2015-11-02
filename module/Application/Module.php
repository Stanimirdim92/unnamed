<?php

/**
 * @copyright  2015 (c) Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 *
 * @version    0.0.21
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
     * @var \Zend\ServiceManager\ServiceManager
     */
    private $service;

    /**
     * Setup module layout.
     *
     * @param $moduleManager ModuleManagerInterface
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
     * @return array|void
     */
    public function onBootstrap(EventInterface $event)
    {
        $app = $event->getApplication();
        $moduleRouteListener = new ModuleRouteListener();
        $eventManager = $app->getEventManager();
        $this->service = $app->getServiceManager();
        $moduleRouteListener->attach($eventManager);

        $sessionManager = $this->service->get('initSession');
        $sessionManager->setName("zpc")->start();
        Container::setDefaultManager($sessionManager);

        $eventManager->attach("dispatch", [$this, 'setTitleAndTranslation'], -10);
        $eventManager->attach("dispatch.error", [$this, "onError"], 2);
    }

    /**
     * Log errors.
     *
     * @param EventInterface $event
     */
    public function onError(EventInterface $event)
    {
        $service = $this->service->get('ErrorHandling');
        $service->logError($event, $this->service);
        $event->stopPropagation();
    }

    /**
     * Handle layout titles onDispatch.
     *
     * @param EventInterface $event
     */
    public function setTitleAndTranslation(EventInterface $event)
    {
        $route = $event->getRouteMatch();
        $title = $this->service->get('ControllerPluginManager')->get("systemsettings");
        $viewHelper = $this->service->get('ViewHelperManager');
        $lang = new Container("translations");
        $translator = $this->service->get('translator');

        /*
         * Load translations.
         */
        $translator->setLocale($lang->languageName)->setFallbackLocale('en');
        $viewModel = $event->getViewModel();
        $viewModel->lang = $translator->getLocale();

        /*
         * Load page title
         */
        $action = ($route->getParam('post') ? ' - ' . $route->getParam('post') : ucfirst($route->getParam('__CONTROLLER__')));

        $headTitleHelper = $viewHelper->get('headTitle');
        $headTitleHelper->append($title->__invoke('general', 'site_name') . " " . $action);
    }

    /**
     * @return array|\Traversable
     */
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }
}
