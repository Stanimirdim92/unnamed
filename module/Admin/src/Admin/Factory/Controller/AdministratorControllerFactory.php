<?php

/**
 * @copyright  2015 (c) Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 *
 * @version    0.0.14
 *
 * @link       TBA
 */

namespace Admin\Factory\Controller;

use Admin\Controller\AdministratorController;
use Zend\Mvc\Controller\ControllerManager;

class AdministratorControllerFactory
{
    /**
     * @{inheritDoc}
     */
    public function __invoke(ControllerManager $controllerManager)
    {
        $serviceLocator = $controllerManager->getServiceLocator();

        $controller = new AdministratorController(
            $serviceLocator->get('FormElementManager')->get('Admin\Form\AdministratorForm')
        );

        return $controller;
    }
}
