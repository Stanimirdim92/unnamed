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
    /**
     * @var TableGateway
     */
    private $_tableGateway;

    /**
     * @var ServiceManager
     */
    private $_serviceManager;

    public function __construct(ServiceManager $sm)
    {
        $this->_serviceManager = $sm;
        $this->_tableGateway = $sm->get("MenuTableGateway");
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
            $select = new Select("menu");
            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new Menu(array(), $this->_serviceManager));
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
     * @param int $id menu id
     * @return Menu
     */
    public function getMenu($id = 0)
    {
        $rowset = $this->_tableGateway->select(array('id' => (int) $id));
        if (!$rowset->current()) 
        {
            throw new \Exception("Oops error.");
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
        if (!$id) 
        {
            $this->_tableGateway->insert($data);
            $menu->id = $this->_tableGateway->lastInsertValue;
        }
        else
        {
            if (!$this->getMenu($id))
            {
                throw new \Exception("Oops error.");
            }
            $this->_tableGateway->update($data, array('id' => $id));
        }
        return $menu;
    }

    /**
     * @param int $id menu id
     * @return Menu
     */
    public function duplicate($id = 0)
    {
        $menu = $this->getMenu($id);
        $clone = $menu->getCopy();
        $this->saveMenu($clone);
        return $clone;
    }
}
