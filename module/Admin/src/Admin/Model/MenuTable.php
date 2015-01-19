<?php
namespace Admin\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;
use Zend\Db\ResultSet\ResultSet;
use Zend\ServiceManager\ServiceManager;

class MenuTable
{
    private $_tableGateway;
    private $_serviceManager;

    public function __construct(ServiceManager $sm)
    {
        $this->_serviceManager = $sm;
        $this->_tableGateway = $sm->get("MenuTableGateway");
    }

    /**
     * Main function for handlin MySQL queries
     *
     * @param  bool   $paginated should we use pagination or no
     * @param  null $where     WHERE condition
     * @param  null $order     ORDER condition
     * @param  null $limit     LIMIT condition
     * @param  null $offset    OFFSET condition
     *
     * @return ResultSet
     */
    public function fetchList($paginated=false, $where=null, $order=null, $limit=null, $offset=null)
    {
        if($paginated)
        {
            $select = new Select("menu");
            if($where!=null)
                $select->where($where);
            if($order!=null)
                $select->order($order);
            if($limit!=null)
                $select->limit($limit);
            if($offset!=null)
                $select->offset($offset);
            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new Menu(array(), $this->_serviceManager));
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
     * @param null $where
     * @param null $order
     * @param null $limit
     * @param null $offset
     * @return unknown
     */
    public function fetchJoin($join, $on, $where=null, $order=null, $limit=null, $offset=null)
    {
        $resultSet = $this->_tableGateway->select(function(Select $select) use ($join, $on, $where, $order, $limit, $offset)
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
     * @param int $id menu id
     * @return Menu
     */
    public function getMenu($id = 0)
    {
        $id  = (int) $id;
        $rowset = $this->_tableGateway->select(array('id' => $id));
        if (!$rowset->current()) 
        {
            throw new \Exception();
        }
        return $rowset->current();
    }

    /**
     * @param int $id menu id
     * @return Menu
     */
    public function deleteMenu($id = 0)
    {
        $this->_tableGateway->delete(array('id' => (int) $id));
    }

    /**
     * @param Menu $menu
     * @return Menu
     */
    public function saveMenu(Menu $menu)
    {
        $data = array(
            'caption'      => (string) $menu->caption,
            'menuOrder'    => (int) $menu->menuOrder,
            'language'     => (int) $menu->language,
            'parent'       => (int) $menu->parent,
            'keywords'     => (string) $menu->keywords,
            'description'  => (string) $menu->description,
            'menutype'     => (int) $menu->menutype,
            'footercolumn' => (int) $menu->footercolumn,
            'menulink'     => (string) $menu->menulink,
        );
        $id = (int)$menu->id;
        if ($id === 0) 
        {
            $this->_tableGateway->insert($data);
            $menu->id = $this->_tableGateway->lastInsertValue;
        }

        if (!$this->getMenu($id)) 
        {
            throw new \Exception();
        }
        $this->_tableGateway->update($data, array('id' => $id));
            
        return $menu;
    }

    /**
     * @param int $id menu id
     * @return Menu
     */
    public function duplicate($id)
    {
        $menu = $this->getMenu($id);
        $clone = $menu->getCopy();
        $this->saveMenu($clone);
		return $clone;
    }
}
