<?php
/**
 * MIT License
 * ===========
 *
 * Copyright (c) 2015 Stanimir Dimitrov <stanimirdim92@gmail.com>
 *
 * Permission is hereby granted, free of charge, to any person obtaining
 * a copy of this software and associated documentation files (the
 * "Software"), to deal in the Software without restriction, including
 * without limitation the rights to use, copy, modify, merge, publish,
 * distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to
 * the following conditions:
 *
 * The above copyright notice and this permission notice shall be included
 * in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS
 * OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
 * IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY
 * CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT,
 * TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE
 * SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 *
 * @category   Admin\Menu
 * @package    ZendPress
 * @author     Stanimir Dimitrov <stanimirdim92@gmail.com>
 * @copyright  2015 Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 * @version    0.03
 * @link       TBA
 */

namespace Admin\Model;

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
    private $_tableGateway = null;

    /**
     * @var ServiceManager
     */
    private $_serviceManager = null;

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
            return new Paginator($paginatorAdapter);
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
        $limit = (int) $limit;
        $offset = (int) $offset;
        if ($pagination)
        {
            
        }
        else
        {
            $resultSet = $this->_tableGateway->select(function(Select $select) use ($join, $on, $where, $order, $limit, $offset)
            {
                //when joining rename all columns from the joined table in order to avoid name clash
                //this means when both tables have a column id the second table will have id renamed to id1
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
    public function getMenu($id = 0, $language = 1)
    {
        $rowset = $this->_tableGateway->select(array('id' => (int) $id, 'language' => (int) $language));
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
    public function saveMenu(Menu $menu = null)
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
        $id = $menu->id;
        $language = $menu->language;
        if (!$id) 
        {
            $this->_tableGateway->insert($data);
            $menu->id = $this->_tableGateway->lastInsertValue;
        }
        else
        {
            if (!$this->getMenu($id, $language))
            {
                throw new \Exception("Oops error.");
            }
            $this->_tableGateway->update($data, array('id' => $id, 'language' => $language));
        }
        unset($id);
        unset($language);
        unset($data);
        return $menu;
    }

    /**
     * @param int $id menu id
     * @return Menu
     */
    public function duplicate($id = 0, $language = 1)
    {
        $menu = $this->getMenu($id, $language);
        $clone = $menu->getCopy();
        $this->saveMenu($clone);
        return $clone;
    }
}
