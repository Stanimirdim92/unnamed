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

use Application\Controller\Plugin\SetLayoutMessages;
use Zend\Mvc\Controller\PluginManager;

class SetLayoutMessagesFactory
{
    /**
     * @{inheritDoc}
     */
    public function __invoke(PluginManager $pluginManager)
    {
        $layout = $pluginManager->getController()->layout();

        $flashmessenger = $pluginManager->get("flashmessenger");

        $plugin = new SetLayoutMessages($layout, $flashmessenger);

        return $plugin;
    }
}
