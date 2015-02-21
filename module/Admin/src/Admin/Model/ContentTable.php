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

    const PRE_AND = "AND";
    const PRE_OR = "OR";
    const PRE_NULL = null;
    const JOIN_INNER = 'inner';
    const JOIN_OUTER = 'outer';
    const JOIN_LEFT = 'left';
    const JOIN_RIGHT = 'right';

    public function __construct(ServiceManager $sm = null)
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
     * @param  null $group     GROUP condition
     * @param  null $order     ORDER condition
     * @param  null $limit     LIMIT condition
     * @param  null $offset    OFFSET condition
     * @return ResultSet|Paginator
     */
    public function fetchList($paginated = false, array $columns = array(), $where = null, $predicate = self::PRE_NULL, $group = null, $order = null, $limit = null, $offset = null)
    {
        $limit = (int) $limit;
        $offset = (int) $offset;
        if($paginated === true)
        {
            $select = new Select("content");
            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new Content(array(), $this->_serviceManager));
            $paginatorAdapter = new DbSelect($this->queryColumns($select, $columns, $where, $predicate, $group, $order, $limit, $offset), $this->_tableGateway->getAdapter(), $resultSetPrototype);
            return new Paginator($paginatorAdapter);
        }
        else
        {
            $resultSet = $this->_tableGateway->select(function(Select $select) use ($columns, $where, $predicate, $group, $order, $limit, $offset)
            {
                $this->queryColumns($select, $columns, $where, $predicate, $group, $order, $limit, $offset);
            });
            $resultSet->buffer();
            return ($resultSet->valid() ? $resultSet : null);
        }
    }

    /**
     * Fetch all records from the DB by joining them
     * 
     * @param string $join    table name
     * @param string $on      table colums
     * @param null $where     WHERE condition
     * @param null $group     GROUP condition
     * @param null $order     ORDER condition
     * @param null $limit     LIMIT condition
     * @param null $offset    OFFSET condition
     * @return ResultSet
     */
    public function fetchJoin($pagination = false, $join = '', $on = '', $joinType = self::JOIN_INNER, $where = null, $group = null, $order = null, $limit = null, $offset = null)
    {
        $limit = (int) $limit;
        $offset = (int) $offset;
        if (!in_array($joinType, array(self::JOIN_INNER, self::JOIN_RIGHT, self::JOIN_LEFT, self::JOIN_OUTER)))
        {
            $joinType = self::JOIN_INNER;
        }

        if ($pagination === true)
        {
            
        }
        else
        {
            $resultSet = $this->_tableGateway->select(function(Select $select) use ($join, $on, $joinType, $where, $group, $order, $limit, $offset)
            {
                //when joining rename all columns from the joined table in order to avoid name clash
                //this means when both tables have a column id the second table will have id renamed to id1
                $select->join($join, $on, array("id1"=>"id"), $joinType);
                $this->queryColumns($select, array(), $where, self::PRE_NULL, $group, $order, $limit, $offset);
            });
            $resultSet->buffer();
            return ($resultSet->valid() ? $resultSet : null);
        }
    }

    /**
     * Prepare all statements before quering the database
     *
     * @param  Select $select 
     * @param  array  $columns
     * @param  null $where
     * @param  null $group
     * @param  null $predicate
     * @param  null $order
     * @param  null $limit
     * @param  null $offset
     *
     * @return Select
     */
    private function queryColumns(Select $select, array $columns = array(), $where = null, $predicate = self::PRE_NULL, $group = null, $order = null, $limit = null, $offset = null)
    {
        if(is_array($columns) && !empty($columns))
            $select->columns($columns);
        if(is_array($where) && !empty($where))
        {
            if (!in_array($predicate, array(self::PRE_AND, self::PRE_OR, self::PRE_NULL)))
            {
                $predicate = self::PRE_NULL;
            }
            $select->where($where, $predicate);
        }
        else if ($where != null)
        {
            $select->where($where);
        }
        if($group != null)
            $select->group($group);
        if($order != null)
            $select->order($order);
        if($limit != null)
            $select->limit($limit);
        if($offset != null)
            $select->offset($offset);
        return $select;
    }
    
    /**
     * @param int $id content id
     * @param int $language user language
     * @throws Exception If content is not found
     * @return Content
     */
    public function getContent($id = 0, $language = 1)
    {
        $rowset = $this->_tableGateway->select(array('id' => (int) $id, "language" => (int) $language));
        if (!$rowset->current()) 
        {
            throw new \Exception("Couldn't find content");
        }
        return $rowset->current();
    }

    /**
     * Delete a content based on the provided id and language
     * 
     * @param int $id content id
     * @param int $language user language
     * @throws Exception If content is not found
     * @return Content
     */
    public function deleteContent($id = 0, $language = 1)
    {
        if (!$this->getContent($id, $language))
        {
            throw new \Exception("Couldn't delete content");
        }
        $this->_tableGateway->delete(array('id' => (int) $id));
    }
    
    /**
     * Save or update content based on the provided id and language
     *
     * @param  Content|null $content
     * @throws Exception If content is not found
     * @return Content
     */
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
        $id = (int) $content->id;
        $language = (int) $content->language;
        if (!$id) 
        {
            $this->_tableGateway->insert($data);
            $content->id = $this->_tableGateway->lastInsertValue;
        }
        else
        {
            if (!$this->getContent($id, $language))
            {
                throw new \Exception("Couldn't save content");
            }
            $this->_tableGateway->update($data, array('id' => $id, 'language' => $language));
        }
        unset($id, $language, $data);
        return $content;
    }
    
    /**
     * duplicate a content 
     *
     * @param  int    $id
     * @param  int    $language
     *
     * @return Content
     */
    public function duplicate($id = 0, $language = 1)
    {
        $content = $this->getContent($id, $language);
        if (!$content)
        {
            throw new \Exception("Couldn't clone content");
        }
        $clone = $content->getCopy();
        $this->saveContent($clone);
		return $clone;
    }
}
