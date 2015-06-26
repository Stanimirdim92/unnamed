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
 * @package    Unnamed
 * @author     Stanimir Dimitrov <stanimirdim92@gmail.com>
 * @copyright  2015 Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 * @version    0.0.3
 * @link       TBA
 */

namespace Admin\Model;

use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;
use Zend\ServiceManager\ServiceManager;

class MenuTable
{
    /**
     * @var TableGateway
     */
    private $tableGateway = null;

    /**
     * @var ServiceManager
     */
    private $serviceManager = null;

    /**
     * Preducate contstants
     */
    const PRE_AND = "AND";
    const PRE_OR = "OR";
    const PRE_NULL = null;

    /**
     * @param ServiceManager|null $sm
     * @param TableGateway|null   $tg
     */
    public function __construct(ServiceManager $sm = null, TableGateway $tg = null)
    {
        $this->serviceManager = $sm;
        $this->tableGateway = $tg;
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
     * @return ResultSet|Paginator|null
     */
    public function fetchList($paginated = false, array $columns = [], $where = null, $predicate = self::PRE_NULL, $group = null, $order = null, $limit = null, $offset = null)
    {
        if ((bool) $paginated === true) {
            $paginatorAdapter = new DbSelect($this->prepareQuery(new Select("menu"), $columns, $where, $predicate, $group, $order, $limit, $offset), $this->tableGateway->getAdapter(), $this->tableGateway->getResultSetPrototype());
            return new Paginator($paginatorAdapter);
        } else {
            $select = $this->prepareQuery(new Select("menu"), $columns, $where, $predicate, $group, $order, (int) $limit, (int) $offset);
            $resultSet = $this->tableGateway->selectWith($select);
            $resultSet->buffer();
            if ($resultSet instanceof \Zend\Db\ResultSet\ResultSet && $resultSet->isBuffered()) {
                return ($resultSet->valid() && $resultSet->count() > 0 ? $resultSet : null);
            }
            return null;
        }
    }

    /**
     * @param bool $pagination
     * @param string $join
     * @param array $tbl1OneCols - content table
     * @param array $tbl2OneCols - the joined table
     * @param string $on
     * @param string $joinType
     * @param null $where
     * @param null $group
     * @param null $order
     * @param null $limit
     * @param null $offset
     *
     * @return ResultSet|Paginator|null
     */
    public function fetchJoin($pagination = false, $join = '', array $tbl1OneCols = [], array $tbl2OneCols = [], $on = '', $joinType = self::JOIN_INNER, $where = null, $group = null, $order = null, $limit = null, $offset = null)
    {
        $select = new Select("menu");
        $select->join($join, $on, $tbl2OneCols, $joinType);
        if ((bool) $pagination === true) {
            $paginatorAdapter = new DbSelect($this->prepareQuery($select, $tbl1OneCols, $where, self::PRE_NULL, $group, $order, (int) $limit, (int) $offset), $this->tableGateway->getAdapter(), $this->tableGateway->getResultSetPrototype());
            return new Paginator($paginatorAdapter);
        } else {
            $result = $this->prepareQuery($select, $tbl1OneCols, $where, self::PRE_NULL, $group, $order, (int) $limit, (int) $offset);
            $resultSet = $this->tableGateway->selectWith($result);
            $resultSet->buffer();
            if ($resultSet instanceof \Zend\Db\ResultSet\ResultSet && $resultSet->isBuffered()) {
                return ($resultSet->valid() && $resultSet->count() > 0 ? $resultSet : null);
            }
            return null;
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
    private function prepareQuery(Select $select, array $columns = [], $where = null, $predicate = self::PRE_NULL, $group = null, $order = null, $limit = null, $offset = null)
    {
        if (is_array($columns) && !empty($columns)) {
            $select->columns($columns);
        }
        if (is_array($where) && !empty($where)) {
            if (!in_array($predicate, [self::PRE_AND, self::PRE_OR, self::PRE_NULL])) {
                $predicate = self::PRE_NULL;
            }
            $select->where($where, $predicate);
        } elseif ($where != null) {
            $select->where($where);
        }
        if ($group != null) {
            $select->group($group);
        }
        if ($order != null) {
            $select->order($order);
        }
        if ($limit != null) {
            $select->limit($limit);
        }
        if ($offset != null) {
            $select->offset($offset);
        }
        return $select;
    }

    /**
     * @param int $id menu id
     * @param int $language user language
     * @throws Exception If menu is not found
     * @return Menu
     */
    public function getMenu($id = 0, $language = 1)
    {
        $rowset = $this->tableGateway->select(['id' => (int) $id, 'language' => (int) $language]);
        $rowset->buffer();
        if (!$rowset->current()) {
            throw new \RuntimeException("Couldn't find menu");
        }
        return $rowset->current();
    }

    /**
     * Delete a menu based on the provided id and language
     *
     * @param int $id menu id
     * @param int $language user language
     * @throws Exception If menu is not found
     * @return Menu
     */
    public function deleteMenu($id = 0, $language = 1)
    {
        if ($this->getMenu($id, $language)) {
            $this->tableGateway->delete(['id' => (int) $id, "language" => (int) $language]);
        }
    }

    /**
     * Save or update menu based on the provided id and language
     *
     * @param  Menu|null $menu
     * @throws Exception If menu is not found
     * @return Menu
     */
    public function saveMenu(Menu $menu = null)
    {
        $data = [
            'caption'      => (string) $menu->caption,
            'menuOrder'    => (int) $menu->menuOrder,
            'language'     => (int) $menu->language,
            'parent'       => (int) $menu->parent,
            'keywords'     => (string) $menu->keywords,
            'description'  => (string) $menu->description,
            'menutype'     => (int) $menu->menutype,
            'footercolumn' => (int) $menu->footercolumn,
            'menulink'     => (string) $menu->menulink,
        ];
        $id = (int) $menu->id;
        $language = (int) $menu->language;
        if (!$id) {
            $this->tableGateway->insert($data);
            $menu->id = $this->tableGateway->lastInsertValue;
        } else {
            if ($this->getMenu($id, $language)) {
                throw new \RuntimeException("Couldn't save menu");
                $this->tableGateway->update($data, ['id' => $id, 'language' => $language]);
            }
        }
        unset($id, $language, $data);
        return $menu;
    }

    /**
     * duplicate a content
     *
     * @param  int    $id
     * @param  int    $language
     *
     * @return Menu
     */
    public function duplicate($id = 0, $language = 1)
    {
        $menu = $this->getMenu($id, $language);
        $this->saveMenu($menu->getCopy());
        return $clone;
    }
}
