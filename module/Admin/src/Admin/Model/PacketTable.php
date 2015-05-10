<?php
namespace Admin\Model;

use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;
use Zend\Db\ResultSet\ResultSet;
use Zend\ServiceManager\ServiceManager;

class PacketTable
{
    private $tableGateway;
    private $serviceManager;

    public function __construct(ServiceManager $sm)
    {
        $this->serviceManager = $sm;
        $this->tableGateway = $sm->get("PacketTableGateway");
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
            $select = new Select("packet");
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
            $resultSetPrototype->setArrayObjectPrototype(new Packet(null, $this->serviceManager));
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
    public function fetchJoin($join, $on, $where=null, $order=null, $limit=null, $offset=null)
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
     * @return Packet
     */
    public function getPacket($id = 0)
    {
        $id  = (int) $id;
        $rowset = $this->tableGateway->select(['id' => $id]);
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception();
        }
        return $row;
    }

    public function deletePacket($id)
    {
        $this->tableGateway->delete(['id' => (int) $id]);
    }

    public function savePacket(Packet $packet)
    {
        $data = [
            'diskspace' => (string) $packet->diskspace,
            'bandwidth' => (string) $packet->bandwidth,
            'domains' => (string) $packet->domains,
            'dedictip' => (int) $packet->dedictip,
            'domainreg' => (string) $packet->domainreg,
            'support' => (string) $packet->support,
            'webeditor' => (string) $packet->webeditor,
            'price' => (int) $packet->price,
            'type' => (int) $packet->type,
            'text' => (string) $packet->text,
            'discount' => (int) $packet->discount,
            'language' => (int) $packet->language,
            'dollar' => (float) $packet->dollar,
            'euro' => (float) $packet->euro,
        ];
        $id = (int)$packet->id;
        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getPacket($id)) {
                $this->tableGateway->update($data, ['id' => $id]);
            } else {
                throw new \Exception();
            }
        }
        return $packet;
    }

    public function duplicate($id)
    {
        $packet = $this->getPacket($id);
        $clone = $packet->getCopy();
        $this->savePacket($clone);
        return $clone;
    }
}
