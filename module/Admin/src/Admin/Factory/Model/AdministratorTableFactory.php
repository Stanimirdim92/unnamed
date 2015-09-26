<?php

/**
 * @copyright  2015 (c) Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 *
 * @version    0.0.14
 *
 * @link       TBA
 */

namespace Admin\Factory\Model;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Admin\Model\Administrator;
use Admin\Model\AdministratorTable;

class AdministratorTableFactory implements FactoryInterface
{
    /**
     * @{inheritDoc}
     */
    public function createService(ServiceLocatorInterface $sm = null)
    {
        $resultSetPrototype = new ResultSet();
        $resultSetPrototype->setArrayObjectPrototype(new Administrator());
        $db = $sm->get('Zend\Db\Adapter\Adapter');

        $tableGateway = new TableGateway('administrator', $db, null, $resultSetPrototype);
        $table = new AdministratorTable($tableGateway);

        return $table;
    }
}
