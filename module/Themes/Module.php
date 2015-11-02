<?php

/**
 * @copyright  2015 (c) Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 *
 * @version    0.0.21
 *
 * @link       TBA
 */

namespace Themes;

use Zend\EventManager\EventInterface;
use Themes\Service\ReloadService;
use Zend\ModuleManager\Feature\BootstrapListenerInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\InitProviderInterface;
use Zend\ModuleManager\ModuleManagerInterface;

final class Module implements ConfigProviderInterface, BootstrapListenerInterface, InitProviderInterface
{
    /**
     * @var \Zend\getServiceManager\ServiceManager
     */
    private $service;

    /**
     * Setup module layout.
     *
     * @param  $moduleManager ModuleManager
     */
    public function init(ModuleManagerInterface $moduleManager)
    {
        $moduleManager->getEventManager()->getSharedManager()->attach(
            __NAMESPACE__,
            "dispatch",
            function (EventInterface $e) {
                $e->getTarget()->layout('layout/layout');
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
        $this->service = $app->getServiceManager();
        $eventManager = $app->getEventManager();
        $sharedEventManager = $eventManager->getSharedManager();

        $eventManager->attach("render", [$this,'loadTheme'], 100);
        $sharedEventManager->attach(ReloadService::class, 'reload', [$this, 'reloadConfig'], 101);
    }

    /**
     * Listen for theme change and override Config.
     */
    public function reloadConfig()
    {
        $request = $this->service->get('Request');

        $config = $this->service->get('Config');
        $this->service->setAllowOverride(true);
        $config['theme']['name'] = $request->getPost()['themeName'];
        $this->service->setService('Config', $config);
        $this->service->setAllowOverride(false);
    }

    /**
     * Setup theme.
     *
     * @param EventInterface $event
     */
    public function loadTheme(EventInterface $event)
    {
        /**
         * Exclude modules that doesn't need a dinamicly changed layout
         */
        if (!in_array($event->getRouteMatch()->getMatchedRouteName(), ["admin", "admin/default", "themes", "themes/default"])) {
            return $this->service->get('initThemes');
        }
    }

    /**
     * @return array|\Traversable
     */
    public function getConfig()
    {
        return include __DIR__.'/config/module.config.php';
    }
}
