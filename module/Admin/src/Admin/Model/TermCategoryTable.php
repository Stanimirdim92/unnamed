<?php
namespace Admin\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;
use Zend\Db\ResultSet\ResultSet;
use Zend\ServiceManager\ServiceManager;

class TermCategoryTable
{
    private $tableGateway;
    private $serviceManager;

    public function __construct(ServiceManager $sm)
    {
        $this->serviceManager = $sm;
        $this->tableGateway = $sm->get("TermCategoryTableGateway");
    }

    /**
     * Fetch all records from the DB
     * @param boolean $paginated
     * @param string $where
     * @param string $order
     * @param string $limit
     * @param string $offset
     * @return unknown
     */
    public function fetchList($paginated=false, $where=null, $order=null, $limit=null, $offset=null)
    {
        if($paginated)
        {
            $select = new Select("termcategory");
            if($where!=null)
                $select->where($where);
            if($order!=null)
                $select->order($order);
            if($limit!=null)
                $select->limit($limit);
            if($offset!=null)
                $select->offset($offset);
            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new TermCategory());
            $paginatorAdapter = new DbSelect($select,$this->tableGateway->getAdapter(),$resultSetPrototype);
            $paginator = new Paginator($paginatorAdapter);
            return $paginator;
        }
        else
        {
            $resultSet = $this->tableGateway->select(function(Select $select)  use ($where, $order, $limit, $offset)
            {
                if($where!=null)
                    $select->where($where);
                if($order!=null)
                    $select->order($order);
                if($limit!=null)
                    $select->limit($limit);
                if($offset!=null)
                    $select->offset($offset);
            });
            $resultSet->buffer();
            return $resultSet;
        }
    }
    
    /**
     * fetch a list of records from the DB
     * (this method is for backward compatibility with existing BOZA Solutions systems)
     */
    public function getList($where, $order, $limit, $offset)
    {
      	return $this->fetchList($where, $order, $limit, $offset);
    }

    /**
     * Fetch all records from the DB by joining them
     * @param string $join
     * @param string $on
     * @param string $where
     * @param string $order
     * @param string $limit
     * @param string $offset
     * @return unknown
     */
    public function fetchJoin($join, $on, $where=null, $order=null, $limit=null, $offset=null)
    {
        $resultSet = $this->tableGateway->select(function(Select $select)  use ($join, $on, $where, $order, $limit, $offset)
        {
            $select->join($join, $on);
            if($where!=null)
                $select->where($where);
            if($order!=null)
                $select->order($order);
            if($limit!=null)
                $select->limit($limit);
            if($offset!=null)
                $select->offset($offset);
        });
        return $resultSet;
    }

    public function getTermCategory($id)
    {
        $id  = (int) $id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) 
        {
            throw new \Exception();
        }
        return $row;
    }

    public function deleteTermCategory($id)
    {
        $this->tableGateway->delete(array('id' => (int) $id));
    }
    
    public function saveTermCategory(TermCategory $termcategory)
    {
        $data = array(
		'name' => (string) $termcategory->name,

        );
        $id = (int)$termcategory->id;
        if ($id == 0) 
        {
            $this->tableGateway->insert($data);
            $termcategory->id = $this->tableGateway->lastInsertValue;
        }
        else 
        {
            if ($this->getTermCategory($id)) 
            {
                $this->tableGateway->update($data, array('id' => $id));
            }
            else 
            {
                throw new \Exception();
            }
        }
        return $termcategory;
    }

    public function duplicate($id)
    {
        $user = $this->getTermCategory($id);
        $clone = $user->getCopy();
        $this->tableGateway->saveTermCategory($clone);
        return $clone;
    }
}
