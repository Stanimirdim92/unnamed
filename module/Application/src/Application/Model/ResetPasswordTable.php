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
 * @version    0.0.12
 * @link       TBA
 */

namespace Application\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Predicate\Expression;

class ResetPasswordTable
{
    /**
     * @var TableGateway
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
     * Main function for handlin MySQL queries
     *
     * @param  bool $paginated              should we use pagination or no
     * @param  array $columns               substitute * with the columns you need
     * @param  null|array|string $where     WHERE condition
     * @param  null $group                  GROUP condition
     * @param  null $order                  ORDER condition
     * @param  null $limit                  IMIT condition
     * @param  null $offset                 OFFSET condition
     * @return HydratingResultSet|null
     */
    public function fetchList(array $columns = [], $where = null, $predicate = self::PRE_NULL, $group = null, $order = null, $limit = null, $offset = null)
    {
        $select = $this->prepareQuery($this->tableGateway->getSql()->select(), $columns, $where, $predicate, $group, $order, (int) $limit, (int) $offset);
        $resultSet = $this->tableGateway->selectWith($select);
        $resultSet->buffer();
        if ($resultSet->isBuffered() && $resultSet->valid() && $resultSet->count() > 0 ) {
            return $resultSet->getDataSource();
        }
        return null;
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
     * This method returns a single row which verifies that this is the user that needs to reset his password
     *
     * @param int $id password id
     * @param int $id user id
     * @throws Exception If content is not found
     * @return ResetPassword
     */
    public function getResetPassword($id = 0, $user = 0)
    {
        $rowset = $this->tableGateway->select(['id' => (int) $id, 'user' => (int) $user]);
        if (!$rowset->current()) {
            throw new \RuntimeException("Couldn't find row");
        }
        return $rowset->current();
    }

    /**
     * Save or update password based on the provided id
     *
     * @param  ResetPassword|null $resetpassword
     * @throws Exception If resetpassword is not found
     * @return ResetPassword
     */
    public function saveResetPassword(ResetPassword $resetpw = null)
    {
        $data = [
            'ip'    => (string) $resetpw->getIp(),
            'user'  => (int) $resetpw->getUser(),
            'date'  => (string) $resetpw->getDate(),
            'token' => (string) $resetpw->getToken(),
        ];
        $id = (int) $resetpw->getId();
        $user = (int) $resetpw->getUser();
        if (!$id) {
            $this->tableGateway->insert($data);
            $resetpw->id = $this->tableGateway->lastInsertValue;
        } else {
            if (!$this->getResetPassword($id, $user)) {
                throw new \RuntimeException("Couldn't find row");
            }
            $this->tableGateway->update($data, ['id' => (int) $id, 'user' => (int) $user]);
        }
        unset($id, $user, $data);
        return $resetpw;
    }
}
