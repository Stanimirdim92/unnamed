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

use Application\Controller\Plugin\InitMetaTags;
use Zend\Mvc\Controller\PluginManager;

class InitMetaTagsFactory
{
    /**
     * @{inheritDoc}
     *
     * @param PluginManager $pluginManager
     *
     * @return InitMetaTags
     */
    public function __invoke(PluginManager $pluginManager)
    {
        /**
         * @var \Zend\View\HelperPluginManager
         */
        $viewHelper = $pluginManager->getController()->getServiceLocator()->get('ViewHelperManager');

        /**
         * @var \Zend\View\Helper\Placeholder\Container $placeholderContainer
         */
        $placeholderContainer = $viewHelper->get("placeholder")->getContainer("customHead");

        /**
         * @var \Zend\View\Helper\HeadMeta $headMeta
         */
        $headMeta = $viewHelper->get("HeadMeta");

        /**
         * @var \Zend\Http\PhpEnvironment\Request $request
         */
        $request = $pluginManager->getController()->getRequest();

        /**
         * @var \Application\Controller\Plugin\SystemSettings
         */
        $systemsettings = $pluginManager->get("systemsettings");

        /**
         * @var InitMetaTags $plugin
         */
        $plugin = new InitMetaTags($placeholderContainer, $headMeta, $request, $systemsettings);

        return $plugin;
    }
}
