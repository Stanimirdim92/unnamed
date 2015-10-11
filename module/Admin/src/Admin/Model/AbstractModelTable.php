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
    protected $tableGateway = null;

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
     * Create join clause.
     *
     * @param string $name - table name to join
     * @param string $on
     * @param array|string $columns - the joined table
     * @param string $joinType
     *
     * @return AbstractModelTable
     */
    public function join($name, $on, $columns = self::SQL_STAR, $joinType = self::JOIN_INNER)
    {
        $this->select->join($name, $on, $columns, $joinType);

        return $this;
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
     *
     * @return AbstractModelTable
     */
    public function columns(array $columns, $prefixColumnsWithTable = true)
    {
        $this->select->columns($columns, $prefixColumnsWithTable);

        return $this;
    }

    /**
     * Create where clause.
     *
     * @method where
     *
     * @param array|string $where
     * @param string $combination One of the PRE_* constants
     *
     * @return AbstractModelTable
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

        return $this;
    }

    /**
     * Create group clause.
     *
     * @method group
     *
     * @param array $group
     *
     * @return AbstractModelTable
     */
    public function group($group)
    {
        $this->select->group($group);

        return $this;
    }

    /**
     * Create having clause.
     *
     * @method having
     *
     * @param array|string $having
     * @param string $combination One of the PRE_* constants
     *
     * @return AbstractModelTable
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

        return $this;
    }

    /**
     * Create order clause.
     *
     * @method order
     *
     * @param array|string $order
     *
     * @return AbstractModelTable
     */
    public function order($order)
    {
        $this->select->order($order);

        return $this;
    }

    /**
     * Create limit clause.
     *
     * @method limit
     *
     * @param int $limit
     *
     * @return AbstractModelTable
     */
    public function limit($limit = 0)
    {
        $this->select->limit((int) $limit);

        return $this;
    }

    /**
     * Create offset clause.
     *
     * @method offset
     *
     * @param int $offset
     *
     * @return AbstractModelTable
     */
    public function offset($offset = 0)
    {
        $this->select->offset((int) $offset);

        return $this;
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
            throw new InvalidArgumentException(sprintf(
                'Class "%s" does not exists or it was not auto loaded.',
                $className
            ));
        }

        return $className;
    }
}
