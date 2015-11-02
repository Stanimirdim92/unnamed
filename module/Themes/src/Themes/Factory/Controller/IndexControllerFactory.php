<?php

/**
 * @copyright  2015 (c) Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 *
 * @version    0.0.21
 *
 * @link       TBA
 */

namespace Themes\Factory\Controller;

use Themes\Controller\IndexController;
use Zend\Mvc\Controller\ControllerManager;

final class IndexControllerFactory
{
    /**
     * {@inheritdoc}
     */
    public function __invoke(ControllerManager $controllerManager)
    {
        $serviceLocator = $controllerManager->getServiceLocator();
        $themesConfig = $serviceLocator->get('getThemesFromDir');
        $reloadService = $serviceLocator->get('reloadService');
        $controller = new IndexController($themesConfig, $reloadService);

        return $controller;
    }
}
