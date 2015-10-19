<?php

/**
 * @copyright  2015 (c) Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 * @version    0.0.18
 * @link       TBA
 */

namespace Themes;

use Zend\EventManager\EventInterface;
use Zend\ModuleManager\Feature\BootstrapListenerInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;

final class Module implements ConfigProviderInterface, BootstrapListenerInterface
{
    /**
     * Listen to the bootstrap event.
     *
     * @param EventInterface $event
     */
    public function onBootstrap(EventInterface $event)
    {
        $app = $event->getApplication();
        $eventManager = $app->getEventManager();
        $serviceManager = $app->getServiceManager();

        $router = $serviceManager->get('router');
        $request = $serviceManager->get('request');
        $matchedRoute = $router->match($request);
        $route = $matchedRoute->getMatchedRouteName();

        $routes = ["admin", "admin/default", "themes", "themes/default"];

        if (!in_array($route, $routes)) {
            $eventManager->attach(["render"], [$this,'loadTheme'], 100);
        }
    }

    /**
     * Setup theme.
     *
     * @param EventInterface $event
     */
    public function loadTheme(EventInterface $event)
    {
        return $event->getApplication()->getServiceManager()->get('initThemes');
    }

    /**
     * @return array|\Traversable
     */
    public function getConfig()
    {
        return include __DIR__.'/config/module.config.php';
    }
}
