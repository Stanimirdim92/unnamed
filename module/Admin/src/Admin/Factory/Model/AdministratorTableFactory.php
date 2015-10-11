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

use Admin\Model\AdministratorTable;
use Zend\ServiceManager\ServiceLocatorInterface;

final class AdministratorTableFactory
{
    /**
     * @{inheritDoc}
     */
    public function __invoke(ServiceLocatorInterface $serviceLocator)
    {
        $table = new AdministratorTable($serviceLocator->get('SD\Adapter'));

        return $table;
    }
}
