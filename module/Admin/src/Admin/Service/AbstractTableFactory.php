<?php

/**
 * @copyright  2015 (c) Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 *
 * @version    0.0.18
 *
 * @link       TBA
 */

namespace Admin\Service;

use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

final class AbstractTableFactory implements AbstractFactoryInterface
{
    public function canCreateServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $className)
    {
        return (class_exists($className) ? true : false);
    }

    public function createServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $className)
    {
        return new $className($serviceLocator->get("Doctrine\ORM\EntityManager"));
    }
}
