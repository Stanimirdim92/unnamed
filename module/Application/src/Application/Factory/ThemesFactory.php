<?php

/**
 * @copyright  2015 (c) Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 *
 * @version    0.0.18
 *
 * @link       TBA
 */

namespace Application\Factory;

use Zend\ServiceManager\ServiceLocatorInterface;

final class ThemesFactory
{
    /**
     * {@inheritDoc}
     */
    public function __invoke(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Config');
        $systemSettings = $serviceLocator->get('ControllerPluginManager')->get("systemsettings");

        if (isset($config["themes"][$systemSettings->__invoke("theme", "name")])) {
            $themes = $config["themes"][$systemSettings->__invoke("theme", "name")];
        } else {
            $themes = $config["themes"]['default']; // default them by default
        }

        if (isset($themes['template_map'])) {
            $map = $serviceLocator->get('ViewTemplateMapResolver');
            $map->merge($themes['template_map']);
        }

        if (isset($themes['template_path_stack'])) {
            $stack = $serviceLocator->get('ViewTemplatePathStack');
            $stack->addPaths($themes['template_path_stack']);
        }

        return new self();
    }
}
