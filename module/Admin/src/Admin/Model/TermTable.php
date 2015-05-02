<?php
namespace Admin\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;
use Zend\Db\ResultSet\ResultSet;
use Zend\ServiceManager\ServiceManager;

class TermTable
{
    private $tableGateway;
    private $serviceManager;

    public function __construct(ServiceManager $sm)
    {
        $this->serviceManager = $sm;
        $this->tableGateway = $sm->get("TermTableGateway");
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
        if ($paginated) {
            $select = new Select("term");
            if ($where!=null) {
                $select->where($where);
            }
            if ($order!=null) {
                $select->order($order);
            }
            if ($limit!=null) {
                $select->limit($limit);
            }
            if ($offset!=null) {
                $select->offset($offset);
            }
            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new Term());
            $paginatorAdapter = new DbSelect($select,$this->tableGateway->getAdapter(),$resultSetPrototype);
            $paginator = new Paginator($paginatorAdapter);
            return $paginator;
        } else {
            $resultSet = $this->tableGateway->select(function (Select $select) use ($where, $order, $limit, $offset) {
                if ($where!=null) {
                    $select->where($where);
                }
                if ($order!=null) {
                    $select->order($order);
                }
                if ($limit!=null) {
                    $select->limit($limit);
                }
                if ($offset!=null) {
                    $select->offset($offset);
                }
            });
            $resultSet->buffer();
            return $resultSet;
        }
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
        $resultSet = $this->tableGateway->select(function (Select $select) use ($join, $on, $where, $order, $limit, $offset) {
            $select->join($join, $on);
            if ($where!=null) {
                $select->where($where);
            }
            if ($order!=null) {
                $select->order($order);
            }
            if ($limit!=null) {
                $select->limit($limit);
            }
            if ($offset!=null) {
                $select->offset($offset);
            }
        });
        return $resultSet;
    }
    
    public function getTerm($id)
    {
        $id  = (int) $id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception();
        }
        return $row;
    }

    public function deleteTerm($id)
    {
        $this->tableGateway->delete(array('id' => (int) $id));
    }

    public function saveTerm(Term $term)
    {
        $data = array(
            'name' => (string) $term->name,
            'termcategory' => (int) $term->termcategory,
        );
        
        $id = (int)$term->id;
        if ($id == 0) {
            $this->tableGateway->insert($data);
            $term->id = $this->tableGateway->lastInsertValue;
        } else {
            if ($this->getTerm($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception();
            }
        }
        return $term;
    }

    public function duplicate($id)
    {
        $user = $this->getTerm($id);
        $clone = $user->getCopy();
        $this->tableGateway->saveTerm($clone);
        return $clone;
    }
}
