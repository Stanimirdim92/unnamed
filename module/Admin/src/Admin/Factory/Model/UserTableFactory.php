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

use Zend\ServiceManager\ServiceLocatorInterface;
use Admin\Model\UserTable;

final class UserTableFactory
{
    /**
     * @{inheritDoc}
     */
    public function __invoke(ServiceLocatorInterface $serviceLocator)
    {
        $table = new UserTable($serviceLocator->get('SD\Adapter'));

        return $table;
    }
}
