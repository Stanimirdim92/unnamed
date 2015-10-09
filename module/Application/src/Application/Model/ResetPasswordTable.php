<?php

/**
 * @copyright  2015 (c) Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 *
 * @version    0.0.16
 *
 * @link       TBA
 */

namespace Application\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Predicate\Expression;
use Application\Exception\RuntimeException;

final class ResetPasswordTable
{
    /**
     * @var TableGateway
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
     * Main function for handlin MySQL queries.
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
        if ($resultSet->isBuffered() && $resultSet->valid() && $resultSet->count() > 0) {
            return $resultSet->getDataSource();
        }
        return;
    }

    /**
     * Prepare all statements before quering the database.
     *
     * @param  Zend\Db\Sql\Select $select
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
     * This method returns a single row which verifies that this is the user that needs to reset his password.
     *
     * @param int $id password id
     * @param int $id user id
     *
     * @throws RuntimeException If row is not found
     *
     * @return ResetPassword
     */
    public function getResetPassword($id = 0, $user = 0)
    {
        $rowset = $this->tableGateway->select(['id' => (int) $id, 'user' => (int) $user]);
        if (!$rowset->current()) {
            throw new RuntimeException("Couldn't find row");
        }
        return $rowset->current();
    }

    /**
     * Save or update password based on the provided id.
     *
     * @param  ResetPassword $resetpassword
     *
     * @return ResetPassword
     */
    public function saveResetPassword(ResetPassword $resetpw)
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
        } else {
            if ($this->getResetPassword($id, $user)) {
                $this->tableGateway->update($data, ['id' => (int) $id, 'user' => (int) $user]);
            }
        }
        unset($id, $user, $data);
        return $resetpw;
    }
}
