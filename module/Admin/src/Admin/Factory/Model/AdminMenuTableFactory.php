<?php

/**
 * @copyright  2015 (c) Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 *
 * @version    0.0.15
 *
 * @link       TBA
 */

namespace Admin\Factory\Model;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Admin\Model\AdminMenu;
use Admin\Model\AdminMenuTable;

class AdminMenuTableFactory implements FactoryInterface
{
    /**
     * @{inheritDoc}
     */
    public function createService(ServiceLocatorInterface $sm = null)
    {
        $resultSetPrototype = new ResultSet();
        $resultSetPrototype->setArrayObjectPrototype(new AdminMenu());
        $db = $sm->get('Zend\Db\Adapter\Adapter');

        $tableGateway = new TableGateway('adminmenu', $db, null, $resultSetPrototype);
        $table = new AdminMenuTable($tableGateway);

        return $table;
    }
}
