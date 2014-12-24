<?php
namespace Admin\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;
use Zend\Db\ResultSet\ResultSet;
use Zend\ServiceManager\ServiceManager;

class LanguageTable
{
    private $_tableGateway;
    private $_serviceManager;

    public function __construct(ServiceManager $sm)
    {
        $this->_serviceManager = $sm;
        $this->_tableGateway = $sm->get("LanguageTableGateway");
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
            $select = new Select("language");
            if($where!=null)
                $select->where($where);
            if($order!=null)
                $select->order($order);
            if($limit!=null)
                $select->limit($limit);
            if($offset!=null)
                $select->offset($offset);
            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new Language(null, $this->_serviceManager));
            $paginatorAdapter = new DbSelect($select,$this->_tableGateway->getAdapter(),$resultSetPrototype);
            $paginator = new Paginator($paginatorAdapter);
            return $paginator;
        }
        else
        {
            $resultSet = $this->_tableGateway->select(function(Select $select)  use ($where, $order, $limit, $offset)
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
        $resultSet = $this->_tableGateway->select(function(Select $select)  use ($join, $on, $where, $order, $limit, $offset)
        {
            //when joining rename all columns from the joined table in order to avoid name clash
            //this means when both tables have a column id the second table will have id renamed to id1
            $select->join($join, $on, array("id1"=>"id"));
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
    
    /**
     * @return Language
     */
    public function getLanguage($id = 0)
    {
        $id  = (int) $id;
        $rowset = $this->_tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) 
        {
            throw new \Exception();
        }
        return $row;
    }

    public function deleteLanguage($id)
    {
        $this->_tableGateway->delete(array('id' => (int) $id));
    }
    
    public function saveLanguage(Language $language)
    {
        $data = array(
            'name' => (string) $language->name,
            'active' => (int) $language->active,
        );

        $id = (int)$language->id;
        if ($id == 0) 
        {
            $this->_tableGateway->insert($data);
            $language->id = $this->_tableGateway->lastInsertValue;
        }
        else 
        {
            if ($this->getLanguage($id)) 
            {
                $this->_tableGateway->update($data, array('id' => $id));
            }
            else 
            {
                throw new \Exception();
            }
        }
        return $language;
    }
    
    public function duplicate($id)
    {
        $language = $this->getLanguage($id);
        $clone = $language->getCopy();
        $this->tableGateway->saveLanguage($clone);
		return $clone;
    }
}
