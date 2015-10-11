<?php

/**
 * @copyright  2015 (c) Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 *
 * @version    0.0.17
 *
 * @link       TBA
 */

namespace Admin\Factory\Controller;

use Admin\Controller\UserController;
use Zend\Mvc\Controller\ControllerManager;

final class UserControllerFactory
{
    /**
     * @{inheritDoc}
     */
    public function __invoke(ControllerManager $controllerManager)
    {
        $serviceLocator = $controllerManager->getServiceLocator();

        $controller = new UserController(
            $serviceLocator->get('FormElementManager')->get('Admin\Form\UserForm')
        );

        return $controller;
    }
}
