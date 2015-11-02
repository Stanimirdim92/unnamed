<?php

/**
 * @copyright  2015 (c) Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 *
 * @version    0.0.21
 *
 * @link       TBA
 */

namespace Admin\Service;

use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

final class AbstractTableFactory implements AbstractFactoryInterface
{
    /**
     * Determine if we can create a service with name
     *
     * @method canCreateServiceWithName
     *
     * @param  ServiceLocatorInterface $services
     * @param  string                  $name
     * @param  string                  $requestedName
     *
     * @return bool
     */
    public function canCreateServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $className)
    {
        if (strpos($className, "Table") && class_exists($className)) {
            return true;
        }

        return false;
    }

    /**
     * Create service with name
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @param string $name
     * @param string $requestedName
     *
     * @return object
     */
    public function createServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $className)
    {
        return new $className($serviceLocator->get("Doctrine\ORM\EntityManager"));
    }
}
