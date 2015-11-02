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

use Application\Controller\Plugin\ErrorCodes;
use Zend\Mvc\Controller\PluginManager;

class ErrorCodesFactory
{
    /**
     * @{inheritDoc}
     */
    public function __invoke(PluginManager $pluginManager)
    {
        $controller = $pluginManager->getController();

        $layout = $controller->layout();

        $response = $controller->getResponse();

        $plugin = new ErrorCodes($layout, $response);

        return $plugin;
    }
}
