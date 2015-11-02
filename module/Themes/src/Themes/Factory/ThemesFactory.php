<?php

/**
 * @copyright  2015 (c) Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 *
 * @version    0.0.21
 *
 * @link       TBA
 */

namespace Themes\Factory;

use Zend\ServiceManager\ServiceLocatorInterface;

final class ThemesFactory
{
    /**
     * {@inheritdoc}
     */
    public function __invoke(ServiceLocatorInterface $serviceLocator)
    {
        $themesConfig = $serviceLocator->get('getThemesFromDir');
        $config = $serviceLocator->get('Config');
        $headScript = $serviceLocator->get('ViewHelperManager')->get('HeadScript');
        $headLink = $serviceLocator->get('ViewHelperManager')->get('headLink');
        $publicDir = '/layouts/'.$config['theme']['name'].DIRECTORY_SEPARATOR;

        /*
         * Get theme name from config and load it.
         *
         * At this point the user has already been selected the new theme he wants to use
         * from indexAction.
         */
        $viewTemplate = $serviceLocator->get('ViewTemplatePathStack');
        $themes = $themesConfig['themes'][$config['theme']['name']];

        if (isset($themes['template_path_stack'])) {
            $viewTemplate->addPaths($themes['template_path_stack']);
        }

        if (isset($themes['template_map'])) {
            $viewTemplate = $serviceLocator->get('ViewTemplateMapResolver');
            $viewTemplate->merge($themes['template_map']);
        }

        foreach ($themes['css'] as $key => $file) {
            $headLink->prependStylesheet($publicDir.$file);
        }

        foreach ($themes['js'] as $key => $file) {
            $headScript->prependFile($publicDir.$file);
        }

        return $viewTemplate;
    }
}
