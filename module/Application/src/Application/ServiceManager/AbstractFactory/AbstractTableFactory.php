<?php

/**
 * @copyright  2015 (c) Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 *
 * @version    0.0.18
 *
 * @link       TBA
 */

namespace Application\ServiceManager\AbstractFactory;

use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AbstractTableFactory implements AbstractFactoryInterface
{
    public function canCreateServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $className)
    {
        $className = "Admin\Model\\".$className;

        if (!class_exists($className)) {
            $className = "Application\Model\\".$className;
        }

        return (class_exists($className) ? true : false);
    }

    public function createServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $className)
    {
        $className = "Admin\Model\\".$className;

        if (!class_exists($className)) {
            $className = "Application\Model\\".$className;
        }

        if (class_exists($className)) {
            return new $className($serviceLocator->get("SD\Adapter"));
        }

        return false;
    }
}
