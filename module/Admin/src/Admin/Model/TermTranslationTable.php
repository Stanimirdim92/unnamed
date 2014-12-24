<?php
namespace Admin\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;
use Zend\Db\ResultSet\ResultSet;
use Zend\ServiceManager\ServiceManager;

class TermTranslationTable
{
    private $tableGateway;
    private $serviceManager;

    public function __construct(ServiceManager $sm)
    {
        $this->serviceManager = $sm;
        $this->tableGateway = $sm->get("TermTranslationTableGateway");
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
            $select = new Select("termTranslation");
            if($where!=null)
                $select->where($where);
            if($order!=null)
                $select->order($order);
            if($limit!=null)
                $select->limit($limit);
            if($offset!=null)
                $select->offset($offset);
            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new TermTranslation());
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

    public function getTermTranslation($id)
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

    public function deleteTermTranslation($id)
    {
        $this->tableGateway->delete(array('id' => (int) $id));
    }

    public function saveTermTranslation(TermTranslation $termTranslation)
    {
        $data = array(
    		'language' => (int) $termTranslation->language,
    		'translation' => (string) $termTranslation->translation,
    		'term' => (int) $termTranslation->term,
        );

        $id = (int)$termTranslation->id;
        if ($id == 0)
        {
            $this->tableGateway->insert($data);
        }
        else
        {
            if ($this->getTermTranslation($id))
            {
                $this->tableGateway->update($data, array('id' => $id));
            }
            else
            {
                throw new \Exception();
            }
        }
    }

    public function duplicate($id)
    {
        $user = $this->getTermTranslation($id);
        $clone = $user->getCopy();
        $this->tableGateway->saveTermTranslation($clone);
        return $clone;
    }
}
