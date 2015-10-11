<?php

/**
 * @copyright  2015 (c) Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 *
 * @version    0.0.17
 *
 * @link       TBA
 */

namespace Application\Factory\Model;

use Zend\ServiceManager\ServiceLocatorInterface;
use Application\Model\ResetPasswordTable;

final class ResetPasswordTableFactory
{
    /**
     * @{inheritDoc}
     */
    public function __invoke(ServiceLocatorInterface $serviceLocator)
    {
        $table = new ResetPasswordTable($serviceLocator->get('SD\Adapter'));

        return $table;
    }
}
