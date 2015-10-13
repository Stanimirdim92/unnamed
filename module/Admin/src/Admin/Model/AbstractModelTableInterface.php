<?php

/**
 * @copyright  2015 (c) Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 *
 * @version    0.0.18
 *
 * @link       TBA
 */

namespace Admin\Model;

interface AbstractModelTableInterface
{
    /**
     * Preducate contstants.
     */
    const PRE_AND = "AND";
    const PRE_OR = "OR";

    /**
     * Fetch results
     *
     * @method fetch
     *
     * @return ResultSet|null
     */
    public function fetch();

    /**
     * Return pagination results
     *
     * @method fetchPagination
     *
     * @return Paginator
     */
    public function fetchPagination();

    /**
     * Perform a select query
     *
     * @method select
     *
     * @param array $select
     *
     * @return ResultSet
     */
    public function select(array $select = []);

    /**
     * Perform a delete query
     *
     * @method delete
     *
     * @param array $delete
     */
    public function delete(array $delete = []);

    /**
     * Perform a insert query
     *
     * @method insert
     *
     * @param array $insert
     */
    public function insert(array $insert = []);

    /**
     * Perform a update query
     *
     * @method update
     *
     * @param array $set
     * @param string|array|\Closure $where
     */
    public function update(array $set = [], $where = null);

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
    public function join($name, $on, $columns = self::SQL_STAR, $joinType = self::JOIN_INNER);

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
     * @param array $columns
     * @param bool  $prefixColumnsWithTable
     *
     * @return AbstractModelTable
     */
    public function columns(array $columns = ["*"], $prefixColumnsWithTable = true);

    /**
     * Create where clause
     *
     * @method where
     *
     * @param array|string $where
     * @param string $combination One of the PRE_* constants
     *
     * @return AbstractModelTable
     */
    public function where($where, $predicate = self::PRE_NULL);

    /**
     * Create group clause
     *
     * @method group
     *
     * @param array $group
     *
     * @return AbstreactModelTable
     */
    public function group($group);

    /**
     * Create having clause
     *
     * @method having
     *
     * @param array|string $having
     * @param string $combination One of the PRE_* constants
     *
     * @return AbstractModelTable
     */
    public function having($having, $predicate = self::PRE_NULL);

    /**
     * Create order clause
     *
     * @method order
     *
     * @param array $order
     *
     * @return AbstreactModelTable
     */
    public function order($order);

    /**
     * Create limit clause
     *
     * @method limit
     *
     * @param int $limit
     *
     * @return AbstreactModelTable
     */
    public function limit($limit = 0);

    /**
     * Create offset clause
     *
     * @method offset
     *
     * @param int $offset
     *
     * @return AbstreactModelTable
     */
    public function offset($offset = 0);
}
