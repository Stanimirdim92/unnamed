<?php

/**
 * @copyright  2015 (c) Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 *
 * @version    0.0.17
 *
 * @link       TBA
 */

namespace Application\Controller\Plugin\Factory;

use Application\Controller\Plugin\GetLayoutMessages;
use Zend\Mvc\Controller\PluginManager;

class GetLayoutMessagesFactory
{
    /**
     * @{inheritDoc}
     */
    public function __invoke(PluginManager $pluginManager)
    {
        $flashmessenger = $pluginManager->get("flashmessenger");

        $plugin = new GetLayoutMessages($flashmessenger);

        return $plugin;
    }
}
