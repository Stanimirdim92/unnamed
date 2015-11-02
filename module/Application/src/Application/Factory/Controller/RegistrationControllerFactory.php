<?php

/**
 * @copyright  2015 (c) Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 *
 * @version    0.0.21
 *
 * @link       TBA
 */

namespace Application\Factory\Controller;

use Application\Controller\RegistrationController;
use Zend\Mvc\Controller\ControllerManager;

final class RegistrationControllerFactory
{
    /**
     * @{inheritDoc}
     */
    public function __invoke(ControllerManager $controllerManager)
    {
        $serviceLocator = $controllerManager->getServiceLocator();

        $controller = new RegistrationController(
            $serviceLocator->get('FormElementManager')->get('Application\Form\RegistrationForm')
        );

        return $controller;
    }
}
