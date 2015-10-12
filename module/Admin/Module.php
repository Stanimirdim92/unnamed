<?php

/**
 * @copyright  2015 (c) Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 *
 * @version    0.0.17
 *
 * @link       TBA
 */

namespace Admin;

use Zend\EventManager\EventInterface;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\BootstrapListenerInterface;
use Zend\ModuleManager\Feature\InitProviderInterface;
use Zend\ModuleManager\ModuleManagerInterface;

final class Module implements AutoloaderProviderInterface, ConfigProviderInterface, BootstrapListenerInterface, InitProviderInterface
{
    /**
     * Setup module layout.
     *
     * @param  $moduleManager ModuleManager
     */
    public function init(ModuleManagerInterface $moduleManager)
    {
        $moduleManager->getEventManager()->getSharedManager()->attach(
            __NAMESPACE__, "dispatch", function (EventInterface $e) {
            $e->getTarget()->layout('layout/admin');
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
        $em = $e->getApplication()->getEventManager();
        $em->attach("dispatch.error", [$this, "onError"]);
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

    /**
     * @return array|\Traversable
     */
    public function getConfig()
    {
        return include __DIR__.'/config/module.config.php';
    }
}
