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
 * @author     Stanimir Dimitrov <stanimirdim92@gmail.com>
 * @copyright  2015 (c) Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 * @version    0.0.7
 * @link       TBA
 */

namespace Admin\Model;

use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Predicate\Expression;

class AdminMenuTable
{
    /**
     * @var TableGateway $tableGateway
     */
    private $tableGateway = null;

    /**
     * Preducate contstants
     */
    const PRE_AND = "AND";
    const PRE_OR = "OR";
    const PRE_NULL = null;

    /**
     * @param TableGateway|null   $tg
     */
    public function __construct(TableGateway $tg = null)
    {
        $this->tableGateway = $tg;
    }

    /**
     * Main function for handling MySQL queries
     *
     * @param  bool $paginated              should we use pagination or no
     * @param  array $columns               substitute * with the columns you need
     * @param  null|array|string $where     WHERE condition
     * @param  null $group                  GROUP condition
     * @param  null $order                  ORDER condition
     * @param  int $limit                   LIMIT condition
     * @param  int $offset                  OFFSET condition
     * @return HydratingResultSet|Paginator|null
     */
    public function fetchList($paginated = false, array $columns = [], $where = null, $predicate = self::PRE_NULL, $group = null, $order = null, $limit = 0, $offset = 0)
    {
        $select = $this->prepareQuery($this->tableGateway->getSql()->select(), $columns, $where, $predicate, $group, $order, (int) $limit, (int) $offset);
        if ((bool) $paginated === true) {
            return new Paginator(new DbSelect($select, $this->tableGateway->getAdapter(), $this->tableGateway->getResultSetPrototype()));
        } else {
            $resultSet = $this->tableGateway->selectWith($select);
            $resultSet->buffer();
            if ($resultSet->isBuffered() && $resultSet->valid() && $resultSet->count() > 0) {
                return $resultSet;
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
     * @param null|array|string $where
     * @param null $group
     * @param null $order
     * @param int $limit
     * @param int $offset
     *
     * @return HydratingResultSet|Paginator|null
     */
    public function fetchJoin($pagination = false, $join = '', array $tbl1OneCols = [], array $tbl2OneCols = [], $on = '', $joinType = self::JOIN_INNER, $where = null, $group = null, $order = null, $limit = 0, $offset = 0)
    {
        $select = $this->tableGateway->getSql()->select();
        $select->join($join, $on, $tbl2OneCols, $joinType);
        $result = $this->prepareQuery($select, $tbl1OneCols, $where, self::PRE_NULL, $group, $order, (int) $limit, (int) $offset);
        if ((bool) $pagination === true) {
            return new Paginator(new DbSelect($result, $this->tableGateway->getAdapter(), $this->tableGateway->getResultSetPrototype()));
        } else {
            $resultSet = $this->tableGateway->selectWith($result);
            $resultSet->buffer();
            if ($resultSet->isBuffered() && $resultSet->valid() && $resultSet->count() > 0) {
                return $resultSet;
            }
            return null;
        }
    }

    /**
     * Prepare all statements before quering the database
     *
     * @param  Select $select
     * @param  array $columns
     * @param  null|array|string $where
     * @param  null $group
     * @param  null $predicate
     * @param  null $order
     * @param  null $limit
     * @param  null $offset
     *
     * @return Zend\Db\Sql\Select
     */
    private function prepareQuery($select, array $columns = [], $where = null, $predicate = self::PRE_NULL, $group = null, $order = null, $limit = null, $offset = null)
    {
        if (!empty($columns)) {
            $select->columns($columns);
        }
        if (is_array($where) && !empty($where)) {
            if (!in_array($predicate, [self::PRE_AND, self::PRE_OR, self::PRE_NULL])) {
                $predicate = self::PRE_NULL;
            }
            $select->where($where, $predicate);
        } elseif ($where != null && is_string($where)) {
            $select->where(new Expression($where));
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
     * @param int $id adminmenu id
     * @throws Exception If adminmenu is not found
     * @return AdminMenu
     */
    public function getAdminMenu($id = 0)
    {
        $rowset = $this->tableGateway->select(['id' => (int) $id]);
        $rowset->buffer();
        if (!$rowset->current()) {
            throw new \RuntimeException("Couldn't find admin menu");
        }
        return $rowset;
    }

    /**
     * Delete a adminmenu based on the provided id and language
     *
     * @param int $id adminmenu id
     * @param int $language user language
     * @throws Exception If adminmenu is not found
     * @return AdminMenu
     */
    public function deleteAdminMenu($id = 0)
    {
        if ($this->getAdminMenu($id)) {
            $this->tableGateway->delete(['id' => (int) $id]);
        }
    }

    /**
     * Save or update menu based on the provided id and language
     *
     * @param  AdminMenu|null $adminMenu
     * @throws Exception If adminmenu is not found
     * @return AdminMenu
     */
    public function saveAdminMenu(AdminMenu $adminMenu = null)
    {
        $data = [
            'caption'     => (string) $adminMenu->getCaption(),
            'description' => (string) $adminMenu->getDescription(),
            'menuOrder'   => (int) $adminMenu->getMenuOrder(),
            'advanced'    => (int) $adminMenu->getAdvanced(),
            'controller'  => (string) $adminMenu->getController(),
            'action'      => (string) $adminMenu->getAction(),
            'class'       => (string) $adminMenu->getClass(),
            'parent'      => (int) $adminMenu->getParent(),
        ];
        $id = (int)$adminMenu->id;
        if (!$id) {
            $this->tableGateway->insert($data);
            $adminMenu->id = $this->tableGateway->lastInsertValue;
        } else {
            if (!$this->getAdminMenu($id)) {
                throw new \RuntimeException("Couldn't save admin menu");
            }
            $this->tableGateway->update($data, ['id' => $id]);
        }
        unset($id, $data);
        return $adminMenu;
    }

    /**
     * duplicate a content
     *
     * @param  int    $id
     * @return AdminMenu
     */
    public function duplicate($id = 0)
    {
        $adminMenu = $this->getAdminMenu($id);
        $clone = $adminMenu->getCopy();
        $this->saveAdminMenu($clone);
        return $clone;
    }
}
