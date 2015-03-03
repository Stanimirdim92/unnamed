<?php 
namespace Admin\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Admin\Model\Menu;
use Admin\Model\MenuTable;

class MenuTableFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $sm = null)
    {
        $resultSetPrototype = new ResultSet();
        $resultSetPrototype->setArrayObjectPrototype(new Menu(array(), $sm));
        $tg = new TableGateway('menu', $sm->get('Zend\Db\Adapter\Adapter'), null, $resultSetPrototype);
        return new MenuTable($sm, $tg);
    }
}

?>