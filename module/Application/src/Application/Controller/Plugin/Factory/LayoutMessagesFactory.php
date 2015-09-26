<?php

/**
 * @copyright  2015 (c) Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 *
 * @version    0.0.14
 *
 * @link       TBA
 */

namespace Application\Controller\Plugin\Factory;

use Application\Controller\Plugin\LayoutMessages;
use Zend\Mvc\Controller\PluginManager;

class LayoutMessagesFactory
{
    /**
     * @{inheritDoc}
     */
    public function __invoke(PluginManager $pluginManager)
    {
        $layout = $pluginManager->getController()->layout();

        $flashmessenger = $pluginManager->get("flashmessenger");

        $plugin = new LayoutMessages($layout, $flashmessenger);

        return $plugin;
    }
}
