<?php

/**
 * @copyright  2015 (c) Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 *
 * @version    0.0.18
 *
 * @link       TBA
 */

namespace Application\Controller\Plugin\Factory;

use Application\Controller\Plugin\Translate;
use Zend\Mvc\Controller\PluginManager;

final class TranslateFactory
{
    /**
     * @{inheritDoc}
     */
    public function __invoke(PluginManager $pluginManager)
    {
        $translator = $pluginManager->getController()->getServiceLocator()->get("translator")->getTranslator();

        $plugin = new Translate($translator);

        return $plugin;
    }
}
