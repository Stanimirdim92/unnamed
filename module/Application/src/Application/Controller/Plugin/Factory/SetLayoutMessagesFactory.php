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

use Application\Controller\Plugin\SetLayoutMessages;
use Zend\Mvc\Controller\PluginManager;

class SetLayoutMessagesFactory
{
    /**
     * @{inheritDoc}
     *
     * @param PluginManager $pluginManager
     *
     * @return SetLayoutMessages
     */
    public function __invoke(PluginManager $pluginManager)
    {
        $layout = $pluginManager->getController()->layout();

        $flashmessenger = $pluginManager->get("flashmessenger");

        $plugin = new SetLayoutMessages($layout, $flashmessenger);

        return $plugin;
    }
}
