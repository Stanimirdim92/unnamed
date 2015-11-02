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

use Application\Controller\ContactController;
use Zend\Mvc\Controller\ControllerManager;

final class ContactControllerFactory
{
    /**
     * @{inheritDoc}
     */
    public function __invoke(ControllerManager $controllerManager)
    {
        $serviceLocator = $controllerManager->getServiceLocator();

        $controller = new ContactController(
            $serviceLocator->get('FormElementManager')->get('Application\Form\ContactForm')
        );

        return $controller;
    }
}
