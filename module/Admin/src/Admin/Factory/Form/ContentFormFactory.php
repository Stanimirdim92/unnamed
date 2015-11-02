<?php

/**
 * @copyright  2015 (c) Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 *
 * @version    0.0.21
 *
 * @link       TBA
 */

namespace Admin\Factory\Form;

use Admin\Form\ContentForm;
use Zend\ServiceManager\ServiceLocatorInterface;

final class ContentFormFactory
{
    /**
     * @{inheritDoc}
     */
    public function __invoke(ServiceLocatorInterface $serviceLocator)
    {
        $services = $serviceLocator->getServiceLocator();

        $entityManager = $services->get('Doctrine\ORM\EntityManager');

        $form = new ContentForm($entityManager);

        return $form;
    }
}
