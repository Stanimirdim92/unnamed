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
 * @category   Admin\Content
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

class ContentTable
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
        $this->_tableGateway = $sm->get("ContentTableGateway");
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
            $select = new Select("content");
            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new Content(array(), $this->_serviceManager));
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
     * @return Content
     */
    public function getContent($id = 0, $language = 1)
    {
        $rowset = $this->_tableGateway->select(array('id' => (int) $id, "language" => (int) $language));
        if (!$rowset->current()) 
        {
            throw new \Exception("Oops error.");
        }
        return $rowset->current();
    }

    public function deleteContent($id = 0)
    {
        $this->_tableGateway->delete(array('id' => (int) $id));
    }
    
    public function saveContent(Content $content = null)
    {
        $data = array(
            'menu'      => (int) $content->menu,
            'title'     => (string) $content->title,
            'preview'   => (string) $content->preview,
            'text'      => (string) $content->text,
            'menuOrder' => (int) $content->menuOrder,
            'type'      => (int) $content->type,
            'date'      => (string) $content->date,
            'language'  => (int) $content->language,
            'titleLink' => (string) $content->titleLink,
        );
        $id = $content->id;
        $language = $content->language;
        if (!$id) 
        {
            $this->_tableGateway->insert($data);
            $content->id = $this->_tableGateway->lastInsertValue;
        }
        else
        {
            if (!$this->getContent($id, $language))
            {
                throw new \Exception("Oops error.");
            }
            $this->_tableGateway->update($data, array('id' => $id, 'language' => $language));
        }
        unset($id);
        unset($language);
        unset($data);
        return $content;
    }
    
    public function duplicate($id = 0, $language = 1)
    {
        $content = $this->getContent($id, $language);
        $clone = $content->getCopy();
        $this->saveContent($clone);
		return $clone;
    }
}
