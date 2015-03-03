<?php
namespace Application\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\ServiceManager\ServiceManager;

class ResetPasswordTable
{
    /**
     * @var TableGateway
     */
    private $_tableGateway = null;

    /**
     * @var ServiceManager
     */
    private $_serviceManager = null;

    public function __construct(ServiceManager $sm = null, TableGateway $tg = null)
    {
        $this->_serviceManager = $sm;
        $this->_tableGateway = $tg;
    }
    
    /**
     * @param int $id password id
     * @param int $id user id
     * @throws Exception If content is not found
     * @return ResetPassword
     */
    public function getResetPassword($id = 0, $user = 0)
    {
        $rowset = $this->_tableGateway->select(array('id' => (int) $id, 'user' => (int) $user));
        if (!$rowset->current())
        {
            throw new \RuntimeException("Couldn't find password");
        }
        return $rowset->current();
    }
    
    /**
     * Save or update password based on the provided id
     *
     * @param  ResetPassword|null $resetpassword
     * @throws Exception If resetpassword is not found
     * @return ResetPassword
     */
    public function saveResetPassword(ResetPassword $resetpw = null)
    {
        $data = array(
            'ip'    => (string) $resetpw->ip,
            'user'  => (int) $resetpw->user,
            'date'  => (string) $resetpw->date,
            'token' => (string) $resetpw->token,
        );
        $id = (int) $resetpw->id;
        $user = (int) $resetpw->user;
        if (!$id) 
        {
            $this->_tableGateway->insert($data);
            $resetpw->id = $this->_tableGateway->lastInsertValue;
        }
        else
        {
            if (!$this->getResetPassword($id, $user))
            {
                throw new \RuntimeException("Oops error.");
            }
            $this->_tableGateway->update($data, array('id' => (int) $id, 'user' => (int) $user));
        }
        unset($id, $user, $data);
        return $resetpw;
    }
}
