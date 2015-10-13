<?php

/**
 * @copyright  2015 (c) Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 *
 * @version    0.0.17
 *
 * @link       TBA
 */

namespace Admin\Model;

use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Predicate\Expression;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Select;
use Admin\Model\AbstractModelTableInterface;
use Admin\Exception\InvalidArgumentException;

abstract class AbstractModelTable implements AbstractModelTableInterface
{
    /**
     * @var TableGateway $tableGateway
     */
    private $tableGateway = null;

    /**
     * @var Select
     */
    private $select = null;

    /**
     * Abstract method to handle all database model connections.
     *
     * @method __construct
     *
     * @param null|string|array $table the database table to work with
     * @param string $model the model class name.
     * @param Adapter $db database addapter
     *
     * @throws InvalidArgumentException
     */
    public function __construct($table = null, $model = null, Adapter $db = null)
    {
        $model = $this->checkClassExistence($model);

        $resultSetPrototype = new ResultSet();
        $resultSetPrototype->setArrayObjectPrototype(new $model());

        $this->select = new Select($table);
        $this->tableGateway = new TableGateway($table, $db, null, $resultSetPrototype);
    }

    /**
     * Fetch results
     *
     * @method fetch
     *
     * @return ResultSet|null
     */
    public function fetch()
    {
        $resultSet = $this->tableGateway->selectWith($this->select);
        $resultSet->buffer();

        if ($resultSet->isBuffered() && $resultSet->valid() && $resultSet->count() > 0) {
            return $resultSet;
        }

        return;
    }

    /**
     * Return pagination results.
     *
     * @method fetchPagination
     *
     * @return Paginator
     */
    public function fetchPagination()
    {
        $dbSelect = new DbSelect($this->select, $this->tableGateway->getAdapter(), $this->tableGateway->getResultSetPrototype());

        return new Paginator($dbSelect);
    }

    /**
     * Perform a select query
     *
     * @method select
     *
     * @param array $select
     *
     * @return ResultSet
     */
    public function select(array $select = [])
    {
        $result = $this->tableGateway->select($select);

        return $result;
    }

    /**
     * Perform a delete query
     *
     * @method delete
     *
     * @param array $delete
     */
    public function delete(array $delete = [])
    {
       $this->tableGateway->delete($delete);
    }

    /**
     * Perform a insert query
     *
     * @method insert
     *
     * @param array $insert
     */
    public function insert(array $insert = [])
    {
        $this->tableGateway->insert($insert);
    }

    /**
     * Perform a update query
     *
     * @method update
     *
     * @param array $set
     * @param string|array|\Closure $where
     */
    public function update(array $set = [], $where = null)
    {
        $this->tableGateway->update($set, $where);
    }

    /**
     * Create join clause.
     *
     * @param string $name - table name to join
     * @param string $on
     * @param array|string $columns - the joined table
     * @param string $joinType
     */
    public function join($name, $on, $columns = self::SQL_STAR, $joinType = self::JOIN_INNER)
    {
        $this->select->join($name, $on, $columns, $joinType);
    }

    /**
     * Specify columns from which to select.
     *
     * Possible valid states:
     *
     *   array(*)
     *
     *   array(value, ...)
     *     value can be strings or Expression objects
     *
     *   array(string => value, ...)
     *     key string will be use as alias,
     *     value can be string or Expression objects
     *
     * @method columns
     *
     * @param array $columns
     * @param bool $prefixColumnsWithTable
     */
    public function columns(array $columns, $prefixColumnsWithTable = true)
    {
        $this->select->columns($columns, $prefixColumnsWithTable);
    }

    /**
     * Create where clause.
     *
     * @method where
     *
     * @param array|string $where
     * @param string $combination One of the PRE_* constants
     */
    public function where($where, $predicate = self::PRE_AND)
    {
        if (is_array($where) && !empty($where)) {
            if (!in_array($predicate, [self::PRE_AND, self::PRE_OR, self::PRE_AND])) {
                $predicate = self::PRE_AND;
            }
            $this->select->where($where, $predicate);
        } elseif (!empty($where) && is_string($where)) {
            $this->select->where(new Expression($where));
        }
    }

    /**
     * Create group clause.
     *
     * @method group
     *
     * @param array $group
     */
    public function group($group)
    {
        $this->select->group($group);
    }

    /**
     * Create having clause.
     *
     * @method having
     *
     * @param array|string $having
     * @param string $combination One of the PRE_* constants
     */
    public function having($having, $predicate = self::PRE_AND)
    {
        if (is_array($having) && !empty($having)) {
            if (!in_array($predicate, [self::PRE_AND, self::PRE_OR, self::PRE_AND])) {
                $predicate = self::PRE_AND;
            }
            $this->select->having($having, $predicate);
        } elseif (!empty($having) && is_string($having)) {
            $this->select->having(new Expression($having));
        }
    }

    /**
     * Create order clause.
     *
     * @method order
     *
     * @param array|string $order
     */
    public function order($order)
    {
        $this->select->order($order);
    }

    /**
     * Create limit clause.
     *
     * @method limit
     *
     * @param int $limit
     */
    public function limit($limit = 0)
    {
        $this->select->limit((int) $limit);
    }

    /**
     * Create offset clause.
     *
     * @method offset
     *
     * @param int $offset
     */
    public function offset($offset = 0)
    {
        $this->select->offset((int) $offset);
    }

    /**
     * See if cass exists..
     *
     * @method checkClassExistence
     *
     * @param string|null $className
     *
     * @throws InvalidArgumentException if class and with namespace doesn't exist
     *
     * @return string - fully qualified class namespace
     */
    private function checkClassExistence($className = null)
    {
        $className = __NAMESPACE__."\\".$className;

        if (!class_exists($className)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Class "%s" does not exists or it was not auto loaded.',
                    $className
                )
            );
        }

        return $className;
    }
}
