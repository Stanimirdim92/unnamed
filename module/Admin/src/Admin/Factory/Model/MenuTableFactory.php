<?php

/**
 * @copyright  2015 (c) Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 *
 * @version    0.0.17
 *
 * @link       TBA
 */

namespace Admin\Factory\Model;

use Admin\Model\MenuTable;
use Zend\ServiceManager\ServiceLocatorInterface;

final class MenuTableFactory
{
    /**
     * @{inheritDoc}
     */
    public function __invoke(ServiceLocatorInterface $serviceLocator)
    {
        $table = new MenuTable($serviceLocator->get('SD\Adapter'));

        return $table;
    }
}
