<?php

/**
 * @copyright  2015 (c) Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 *
 * @version    0.0.15
 *
 * @link       TBA
 */

namespace Admin\Factory\Controller;

use Admin\Controller\LanguageController;
use Zend\Mvc\Controller\ControllerManager;

class LanguageControllerFactory
{
    /**
     * @{inheritDoc}
     */
    public function __invoke(ControllerManager $controllerManager)
    {
        $serviceLocator = $controllerManager->getServiceLocator();

        $controller = new LanguageController(
            $serviceLocator->get('FormElementManager')->get('Admin\Form\LanguageForm')
        );

        return $controller;
    }
}
