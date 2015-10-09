<?php

/**
 * @copyright  2015 (c) Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 *
 * @version    0.0.16
 *
 * @link       TBA
 */

namespace Admin\Model;

use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Predicate\Expression;
use Admin\Exception\RuntimeException;

final class UserTable
{
    /**
     * @var TableGateway $tableGateway
     */
    private $tableGateway = null;

    /**
     * Preducate contstants.
     */
    const PRE_AND = "AND";
    const PRE_OR = "OR";
    const PRE_NULL = null;

    /**
     * @param TableGateway $tg
     */
    public function __construct(TableGateway $tg)
    {
        $this->tableGateway = $tg;
    }

    /**
     * Main function for handling MySQL queries.
     *
     * @param bool $paginated              should we use pagination or no
     * @param array $columns               substitute * with the columns you need
     * @param null|array|string $where     WHERE condition
     * @param null $group                  GROUP condition
     * @param null $order                  ORDER condition
     * @param int $limit                   LIMIT condition
     * @param int $offset                  OFFSET condition
     * @return ResultSet|Paginator|null
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
            return;
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
     * @return ResultSet|Paginator|null
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
            return;
        }
    }

    /**
     * Prepare all statements before quering the database.
     *
     * @param Zend\Db\Sql\Select $select
     * @param array $columns
     * @param null|array|string $where
     * @param null $group
     * @param null $predicate
     * @param null $order
     * @param null $limit
     * @param null $offset
     *
     * @return Zend\Db\Sql\Select
     */
    private function prepareQuery(\Zend\Db\Sql\Select $select, array $columns = [], $where = null, $predicate = self::PRE_NULL, $group = null, $order = null, $limit = null, $offset = null)
    {
        if (!empty($columns)) {
            $select->columns($columns);
        }
        if (is_array($where) && !empty($where)) {
            if (!in_array($predicate, [self::PRE_AND, self::PRE_OR, self::PRE_NULL])) {
                $predicate = self::PRE_NULL;
            }
            $select->where($where, $predicate);
        } elseif (!empty($where) && is_string($where)) {
            $select->where(new Expression($where));
        }
        if (!empty($group)) {
            $select->group($group);
        }
        if (!empty($order)) {
            $select->order($order);
        }
        if (!empty($limit) && $limit > 0) {
            $select->limit($limit);
        }
        if (!empty($offset) && $offset > 0) {
            $select->offset($offset);
        }
        return $select;
    }

    /**
     * @param int $id user id
     *
     * @return User|null
     */
    public function getUser($id = 0)
    {
        $rowset = $this->tableGateway->select(['id' => (int) $id]);
        $rowset->buffer();
        if (!$rowset->current()) {
            return;
        }
        return $rowset;
    }

    /**
     * This method can disable or enable user accounts.
     *
     * @param int $id user id
     * @param int $state 0 - enabled, 1 - disabled
     */
    public function toggleUserState($id = 0, $state = 0)
    {
        if ($this->getUser($id)) {
            $this->tableGateway->update(["deleted" => (int) $state, 'admin' => 0], ['id' => (int) $id]);
        }
    }

    /**
     * Update user based on the provided id.
     *
     * @param  User $use
     *
     * @throws RuntimeException
     *
     * @return User
     */
    public function saveUser(User $user)
    {
        $data = [
            'name'       => (string) $user->getName(),
            'surname'    => (string) $user->getSurname(),
            'password'   => (string) $user->getPassword(),
            'email'      => (string) $user->getemail(),
            'birthDate'  => (string) $user->getBirthDate(),
            'lastLogin'  => (string) $user->getLastLogin(),
            'deleted'    => (int) $user->getDeleted(),
            'image'      => (string) $user->getImage(),
            'registered' => (string) $user->getRegistered(),
            'hideEmail'  => (int) $user->getHideEmail(),
            'ip'         => (string) $user->getIp(),
            'admin'      => (int) $user->getAdmin(),
            'language'   => (int) $user->getLanguage(),
        ];

        $id = (int) $user->getId();
        if (!$id) {
            $this->tableGateway->insert($data);
        } else {
            if (!$this->getUser($id)) {
                throw new RuntimeException("User not saved");
            }
            $this->tableGateway->update($data, ['id' =>(int)  $id]);
        }
        unset($id, $data);
        return $user;
    }
}
