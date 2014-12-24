<?php
namespace Admin\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;
use Zend\Db\ResultSet\ResultSet;
use Zend\ServiceManager\ServiceManager;

class ContentTable
{
    private $_tableGateway;
    private $_serviceManager;

    public function __construct(ServiceManager $sm)
    {
        $this->_serviceManager = $sm;
        $this->_tableGateway = $sm->get("ContentTableGateway");
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
            $select = new Select("content");
            if($where!=null)
                $select->where($where);
            if($order!=null)
                $select->order($order);
            if($limit!=null)
                $select->limit($limit);
            if($offset!=null)
                $select->offset($offset);
            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new Content(null, $this->_serviceManager));
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
     * @return Content
     */
    public function getContent($id = 0)
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

    public function deleteContent($id)
    {
        $this->_tableGateway->delete(array('id' => (int) $id));
    }
    
    public function saveContent(Content $content)
    {
        $data = array(
        'menu' => (int) $content->menu,
        'title' => (string) $content->title,
        'preview' => (string) $content->preview,
        'extract' => (string) $content->extract,
        'text' => (string) $content->text,
        'menuOrder' => (int) $content->menuOrder,
        'type' => (int) $content->type,
        'date' => (string) $content->date,
        'language' => (int) $content->language,

        );
        $id = (int)$content->id;
        if ($id == 0) 
        {
            $this->_tableGateway->insert($data);
            $content->id = $this->_tableGateway->lastInsertValue;
        }
        else 
        {
            if ($this->getContent($id)) 
            {
                $this->_tableGateway->update($data, array('id' => $id));
            }
            else 
            {
                throw new \Exception();
            }
        }
        return $content;
    }
    
    public function duplicate($id)
    {
        $content = $this->getContent($id);
        $clone = $content->getCopy();
        $this->saveContent($clone);
		return $clone;
    }
}
