<?php

/**
 * @copyright  2015 (c) Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 *
 * @version    0.0.17
 *
 * @link       TBA
 */

namespace Application\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\ServiceManager\ServiceLocatorInterface;

final class GetTableModel extends AbstractPlugin
{
    /**
     * @var ServiceLocatorInterface $serviceLocator
     */
    private $serviceLocator = null;

    /**
     * @param ServiceLocatorInterface $serviceLocator
     */
    public function __construct(ServiceLocatorInterface $serviceLocator = null)
    {
        $this->serviceLocator = $serviceLocator;
    }

    /**
     * @param string $tableName
     *
     * @return object|null
     */
    public function __invoke($tableName)
    {
        if ($this->serviceLocator->has($tableName."table")) {
            return $this->serviceLocator->get($tableName."table");
        }
        return;
    }
}
