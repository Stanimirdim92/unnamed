<?php 
namespace Application\Factory;

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
        $resultSetPrototype->setArrayObjectPrototype(new ResetPassword(array(), $sm));
        $tg = new TableGateway('resetpassword', $sm->get('Zend\Db\Adapter\Adapter'), null, $resultSetPrototype);
        return new ResetPasswordTable(null, $tg);
    }
}

?>