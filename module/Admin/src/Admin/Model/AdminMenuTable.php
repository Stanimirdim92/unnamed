<?php
namespace Admin\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;
use Zend\Db\ResultSet\ResultSet;
use Zend\ServiceManager\ServiceManager;

class AdminMenuTable
{
    private $_tableGateway;
    private $_serviceManager;

    public function __construct(ServiceManager $sm)
    {
        $this->_serviceManager = $sm;
        $this->_tableGateway = $sm->get("AdminMenuTableGateway");
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
            $select = new Select("adminmenu");
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
            $resultSetPrototype->setArrayObjectPrototype(new AdminMenu(null, $this->_serviceManager));
            $paginatorAdapter = new DbSelect($select,$this->_tableGateway->getAdapter(),$resultSetPrototype);
            $paginator = new Paginator($paginatorAdapter);
            return $paginator;
        } else {
            $resultSet = $this->_tableGateway->select(function (Select $select) use ($where, $order, $limit, $offset) {
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
        $resultSet = $this->_tableGateway->select(function (Select $select) use ($join, $on, $where, $order, $limit, $offset) {
            //when joining rename all columns from the joined table in order to avoid name clash
            //this means when both tables have a column id the second table will have id renamed to id1
            $select->join($join, $on, array("id1"=>"id"));
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
    
    /**
     * @return AdminMenu
     */
    public function getAdminMenu($id)
    {
        $id  = (int) $id;
        $rowset = $this->_tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception();
        }
        return $row;
    }

    public function deleteAdminMenu($id)
    {
        $this->_tableGateway->delete(array('id' => (int) $id));
    }
    
    public function saveAdminMenu(AdminMenu $adminMenu)
    {
        $data = array(
            'caption' => (string) $adminMenu->caption,
            'description' => (string) $adminMenu->description,
            'menuOrder' => (int) $adminMenu->menuOrder,
            'advanced' => (int) $adminMenu->advanced,
            'controller' => (string) $adminMenu->controller,
            'action' => (string) $adminMenu->action,
            'class' => (string) $adminMenu->class,
            'parent' => (int) $adminMenu->parent,
        );
        $id = (int)$adminMenu->id;
        if ($id == 0) {
            $this->_tableGateway->insert($data);
            $adminMenu->id = $this->_tableGateway->lastInsertValue;
        } else {
            if ($this->getAdminMenu($id)) {
                $this->_tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception();
            }
        }
        return $adminMenu;
    }
    
    public function duplicate($id)
    {
        $adminMenu = $this->getAdminMenu($id);
        $clone = $adminMenu->getCopy();
        $this->tableGateway->saveAdminMenu($clone);
        return $clone;
    }
}
