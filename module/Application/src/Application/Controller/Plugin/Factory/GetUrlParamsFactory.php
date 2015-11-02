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

use Application\Controller\Plugin\GetUrlParams;
use Zend\Mvc\Controller\PluginManager;

class GetUrlParamsFactory
{
    /**
     * @{inheritDoc}
     *
     * @param PluginManager $pluginManager
     *
     * @return GetUrlParams
     */
    public function __invoke(PluginManager $pluginManager)
    {
        $params = $pluginManager->get("params");

        $plugin = new GetUrlParams($params);

        return $plugin;
    }
}
