<?php
namespace Admin\Model;

use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;
use Zend\Db\ResultSet\ResultSet;
use Zend\ServiceManager\ServiceManager;

class PurchaseTable
{
    private $tableGateway;
    private $serviceManager;

    public function __construct(ServiceManager $sm)
    {
        $this->serviceManager = $sm;
        $this->tableGateway = $sm->get("PurchaseTableGateway");
    }

    /**
     * Fetch all records from the DB
     * @param boolean $paginated
     * @param string $where
     * @param string $order
     * @param string $limit
     * @param string $offset
     * @return unknown
     */
    public function fetchList($paginated=false, $where=null, $order=null, $limit=null, $offset=null)
    {
        if ($paginated) {
            $select = new Select("purchase");
            if ($where!=null) {
                $select->where($where);
            }
            if ($order!=null) {
                $select->order($order);
            }
            if ($limit!=null) {
                $select->limit($limit);
            }
            if ($offset!=null) {
                $select->offset($offset);
            }
            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new Purchase(null, $this->serviceManager));
            $paginatorAdapter = new DbSelect($select, $this->tableGateway->getAdapter(), $resultSetPrototype);
            $paginator = new Paginator($paginatorAdapter);
            return $paginator;
        } else {
            $resultSet = $this->tableGateway->select(function (Select $select) use ($where, $order, $limit, $offset) {
                if ($where!=null) {
                    $select->where($where);
                }
                if ($order!=null) {
                    $select->order($order);
                }
                if ($limit!=null) {
                    $select->limit($limit);
                }
                if ($offset!=null) {
                    $select->offset($offset);
                }
            });
            $resultSet->buffer();
            return $resultSet;
        }
    }

    /**
     * Fetch all records from the DB by joining them
     * @param string $join
     * @param string $on
     * @param string $where
     * @param string $order
     * @param string $limit
     * @param string $offset
     * @return unknown
     */
    public function fetchJoin($paginated=false, $join, $on, $where=null, $order=null, $limit=null, $offset=null)
    {
        $resultSet = $this->tableGateway->select(function (Select $select) use ($join, $on, $where, $order, $limit, $offset) {
            //when joining rename all columns from the joined table in order to avoid name clash
            //this means when both tables have a column id the second table will have id renamed to id1
            $select->join($join, $on, ["id1"=>"id"]);
            if ($where!=null) {
                $select->where($where);
            }
            if ($order!=null) {
                $select->order($order);
            }
            if ($limit!=null) {
                $select->limit($limit);
            }
            if ($offset!=null) {
                $select->offset($offset);
            }
        });
        return $resultSet;
    }

    /**
     * @return Purchase
     */
    public function getPurchase($id = 0)
    {
        $id  = (int) $id;
        $rowset = $this->tableGateway->select(['id' => $id]);
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception();
        }
        return $row;
    }

    public function deletePurchase($id)
    {
        $this->tableGateway->delete(['id' => (int) $id]);
    }

    public function savePurchase(Purchase $purchase)
    {
        $data = [
            'user' => (string) $purchase->user,
            'purchasedate' => (string) $purchase->purchasedate,
            'packet' => (int) $purchase->packet,
            'currency' => (int) $purchase->currency,
            'packetexpires' => (string) $purchase->packetexpires,
            'payed' => (bool) $purchase->payed,
            'active' => (bool) $purchase->active,
            'money' => (float) $purchase->money,
            'currency' => (int) $purchase->currency,
        ];
        $id = (int)$purchase->id;
        if ($id == 0) {
            $this->tableGateway->insert($data);
            $purchase->id = $this->tableGateway->lastInsertValue;
        } else {
            if ($this->getPurchase($id)) {
                $this->tableGateway->update($data, ['id' => $id]);
            } else {
                throw new \Exception();
            }
        }
        return $purchase;
    }
}
