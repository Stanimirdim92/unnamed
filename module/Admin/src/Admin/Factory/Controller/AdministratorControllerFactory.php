<?php

/**
 * @copyright  2015 (c) Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 *
 * @version    0.0.21
 *
 * @link       TBA
 */

namespace Admin\Factory\Controller;

use Admin\Controller\AdministratorController;
use Zend\Mvc\Controller\ControllerManager;

final class AdministratorControllerFactory
{
    /**
     * @{inheritDoc}
     */
    public function __invoke(ControllerManager $controllerManager)
    {
        $serviceLocator = $controllerManager->getServiceLocator();

        $controller = new AdministratorController(
            (object) $serviceLocator->get('FormElementManager')->get('Admin\Form\AdministratorForm')
        );

        return $controller;
    }
}
