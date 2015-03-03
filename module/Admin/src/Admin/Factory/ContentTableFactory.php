<?php 
namespace Admin\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Admin\Model\Content;
use Admin\Model\ContentTable;

class ContentTableFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $sm = null)
    {
        $resultSetPrototype = new ResultSet();
        $resultSetPrototype->setArrayObjectPrototype(new Content(array(), $sm));
        $tg = new TableGateway('content', $sm->get('Zend\Db\Adapter\Adapter'), null, $resultSetPrototype);
        return new ContentTable($sm, $tg);
    }
}

?>