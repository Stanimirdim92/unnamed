<?php

/**
 * @copyright  2015 (c) Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 *
 * @version    0.0.14
 *
 * @link       TBA
 */

namespace Application\Factory\Model;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Application\Model\ResetPassword;
use Application\Model\ResetPasswordTable;

class ResetPasswordTableFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $sm = null)
    {
        $resultSetPrototype = new ResultSet();
        $resultSetPrototype->setArrayObjectPrototype(new ResetPassword());
        $db = $sm->get('Zend\Db\Adapter\Adapter');

        $tableGateway = new TableGateway('resetpassword', $db, null, $resultSetPrototype);
        $table = new ResetPasswordTable($tableGateway);

        return $table;
    }
}
