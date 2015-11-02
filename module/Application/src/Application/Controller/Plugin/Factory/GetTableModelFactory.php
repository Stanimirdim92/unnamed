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

use Application\Controller\Plugin\GetTableModel;
use Zend\Mvc\Controller\PluginManager;

class GetTableModelFactory
{
    /**
     * @{inheritDoc}
     */
    public function __invoke(PluginManager $pluginManager)
    {
        $serviceLocator = $pluginManager->getController()->getServiceLocator();

        $plugin = new GetTableModel($serviceLocator);

        return $plugin;
    }
}
