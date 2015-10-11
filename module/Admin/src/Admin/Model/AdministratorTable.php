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

use Admin\Model\AbstractModelTable;

final class AdministratorTable extends AbstractModelTable
{
    /**
     * @method __construct
     *
     * @param \Zend\Db\Adapter\Adapter $adapter
     */
    public function __construct($adapter)
    {
        parent::__construct('administrator', 'Administrator', $adapter);
    }

    /**
     * @param int $id user id
     *
     * @return Administrator|null
     */
    public function getAdministrator($id = 0)
    {
        $rowset = $this->tableGateway->select(['user' => (int) $id]);
        $rowset->buffer();
        if (!$rowset->current()) {
            return;
        }
        return $rowset->current();
    }

    /**
     * Delete a administrator based on the provided user id.
     *
     * @param int $id user id
     */
    public function deleteAdministrator($id = 0)
    {
        if ($this->getAdministrator($id)) {
            $this->tableGateway->delete(['user' => (int) $id]);
        }
    }

    /**
     * Save or update administrator based on the provided id.
     *
     * @param  Administrator $administrator
     *
     * @return Administrator
     */
    public function saveAdministrator(Administrator $administrator)
    {
        $data = [
            'user' => (int) $administrator->getUser(),
        ];
        $id = (int)$administrator->getId();
        if (!$id) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getAdministrator($id)) {
                $this->tableGateway->update($data, ['user' => $id]);
            }
        }
        unset($id, $data);
        return $administrator;
    }
}
