<?php

/**
 * @copyright  2015 (c) Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 *
 * @version    0.0.21
 *
 * @link       TBA
 */

namespace Application\Controller\Plugin\Factory;

use Application\Controller\Plugin\SystemSettings;
use Zend\Mvc\Controller\PluginManager;

class SystemSettingsFactory
{
    /**
     * @{inheritDoc}
     *
     * @param PluginManager $pluginManager
     *
     * @return SystemSettings
     */
    public function __invoke(PluginManager $pluginManager)
    {
        $serviceLocator = $pluginManager->getController()->getServiceLocator();

        $config = $serviceLocator->get("Config");

        $plugin = new SystemSettings($config["system_config"]);

        return $plugin;
    }
}
