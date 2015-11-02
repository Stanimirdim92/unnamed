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

use Application\Controller\Plugin\Mailing;
use Zend\Mvc\Controller\PluginManager;

class MailingFactory
{
    /**
     * @{inheritDoc}
     *
     * @param PluginManager $pluginManager
     *
     * @return Mailing
     */
    public function __invoke(PluginManager $pluginManager)
    {
        $flashmessenger = $pluginManager->get("flashmessenger");
        $systemsettings = $pluginManager->get("systemsettings");

        $plugin = new Mailing($flashmessenger, $systemsettings);

        return $plugin;
    }
}
