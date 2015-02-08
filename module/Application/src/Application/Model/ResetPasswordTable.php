<?php
namespace Application\Model;

use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;
use Zend\Db\ResultSet\ResultSet;
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

    /**
     * @var string $_tableName
     */
    private $_tableName = "resetpassword";

    public function __construct(ServiceManager $sm)
    {
        $this->_serviceManager = $sm;
        $this->_tableGateway = $sm->get("ResetPasswordTableGateway");
    }

    /**
     * Main function for handlin MySQL queries
     *
     * @param  bool $paginated should we use pagination or no
     * @param  array $columns  substitute * with the columns you need
     * @param  null $where     WHERE condition
     * @param  null $order     ORDER condition
     * @param  null $limit     LIMIT condition
     * @param  null $offset    OFFSET condition
     * @return ResultSet|Paginator
     */
    public function fetchList($paginated = false, array $columns, $where = null, $order = null, $limit = null, $offset = null)
    {
        $limit = (int) $limit;
        $offset = (int) $offset;
        if($paginated)
        {
            $select = new Select($this->_tableName);
            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new ResetPassword(array(), $this->_serviceManager));
            $paginatorAdapter = new DbSelect($this->queryColumns($select, $columns, $where, $order, $limit, $offset), $this->_tableGateway->getAdapter(),$resultSetPrototype);
            $paginator = new Paginator($paginatorAdapter);
            return $paginator;
        }
        else
        {
            $resultSet = $this->_tableGateway->select(function(Select $select) use ($columns, $where, $order, $limit, $offset)
            {
                $this->queryColumns($select, $columns, $where, $order, $limit, $offset);
            });
            $resultSet->buffer();
            return $resultSet;
        }
    }

    /**
     * Fetch all records from the DB by joining them
     * 
     * @param string $join    table name
     * @param string $on      table colums
     * @param null $where     WHERE condition
     * @param null $order     ORDER condition
     * @param null $limit     LIMIT condition
     * @param null $offset    OFFSET condition
     * @return ResultSet
     */
    public function fetchJoin($pagination = false, $join = '', $on = '', $where = null, $order = null, $limit = null, $offset = null)
    {
        if ($pagination)
        {
            
        }
        else
        {
            $resultSet = $this->_tableGateway->select(function(Select $select) use ($join, $on, $where, $order, $limit, $offset)
            {
                //when joining rename all columns from the joined table in order to avoid name clash
                //this means when both tables have a column id the second table will have id renamed to id1
                $limit = (int) $limit;
                $offset = (int) $offset;
                $select->join($join, $on, array("id1"=>"id"));
                $this->queryColumns($select, array(), $where, $order, $limit, $offset);
            });
            return $resultSet;
        }
    }

    /**
     * Prepare all statements before quering the database
     *
     * @param  Select $select 
     * @param  array  $columns
     * @param  null $where  
     * @param  null $order  
     * @param  null $limit  
     * @param  null $offset 
     *
     * @return Select
     */
    private function queryColumns(Select $select, array $columns, $where, $order, $limit, $offset)
    {
        if(is_array($columns) && !empty($columns))
            $select->columns($columns);
        if($where != null)
            $select->where($where);
        if($order != null)
            $select->order($order);
        if($limit != null)
            $select->limit($limit);
        if($offset != null)
            $select->offset($offset);
        return $select;
    }
    
    /**
     * @return ResetPassword
     */
    public function getResetPassword($id = 0)
    {
        $rowset = $this->_tableGateway->select(array('id' => (int) $id));
        if (!$rowset->current()) 
        {
            throw new \Exception("Oops error.");
        }
        return $rowset->current();
    }

    public function deleteResetPassword($id)
    {
        $this->_tableGateway->delete(array('id' => (int) $id));
    }
    
    public function saveResetPassword(ResetPassword $resetpw)
    {
        $data = array(
            'ip'    => (string) $resetpw->ip,
            'user'  => (int) $resetpw->user,
            'date'  => (string) $resetpw->date,
            'token' => (string) $resetpw->token,
        );
        $id = $resetpw->id;
        if (!$id) 
        {
            $this->_tableGateway->insert($data);
            $resetpw->id = $this->_tableGateway->lastInsertValue;
        }
        else
        {
            if (!$this->getResetPassword($id))
            {
                throw new \Exception("Oops error.");
            }
            $this->_tableGateway->update($data, array('id' => $id));
        }
        unset($id);
        unset($data);
        return $resetpw;
    }
}
