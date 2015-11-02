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

use Application\Controller\LoginController;
use Zend\Mvc\Controller\ControllerManager;

final class LoginControllerFactory
{
    /**
     * @{inheritDoc}
     */
    public function __invoke(ControllerManager $controllerManager)
    {
        $serviceLocator = $controllerManager->getServiceLocator();

        $controller = new LoginController(
            $serviceLocator->get('FormElementManager')->get('Application\Form\LoginForm'),
            $serviceLocator->get('doctrine.authenticationservice.orm_default'),
            $serviceLocator->get('FormElementManager')->get('Application\Form\ResetPasswordForm'),
            $serviceLocator->get('FormElementManager')->get('Application\Form\NewPasswordForm')
        );

        return $controller;
    }
}
