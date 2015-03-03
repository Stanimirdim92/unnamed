<?php 
namespace Admin\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Admin\Model\Administrator;
use Admin\Model\AdministratorTable;

class AdministratorTableFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $sm = null)
    {
        $resultSetPrototype = new ResultSet();
        $resultSetPrototype->setArrayObjectPrototype(new Administrator(array(), $sm));
        $tg = new TableGateway('administrator', $sm->get('Zend\Db\Adapter\Adapter'), null, $resultSetPrototype);
        return new AdministratorTable($sm, $tg);
    }
}

?>