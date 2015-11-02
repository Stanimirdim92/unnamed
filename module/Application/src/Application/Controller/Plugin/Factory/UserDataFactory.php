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

use Application\Controller\Plugin\UserData;
use Zend\Mvc\Controller\PluginManager;

class UserDataFactory
{
    /**
     * @{inheritDoc}
     *
     * @param PluginManager $pluginManager
     *
     * @return UserData
     */
    public function __invoke(PluginManager $pluginManager)
    {
        $redirect = $pluginManager->get("redirect");

        $plugin = new UserData($redirect);

        return $plugin;
    }
}
