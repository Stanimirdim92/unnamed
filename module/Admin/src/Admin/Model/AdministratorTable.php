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

final class AdministratorTable
{
    /**
     * @var Doctrine\ORM\EntityManager
     */
    private $entityManager;

    public function __construct($entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param Doctrine\ORM\EntityManager
     */
    public function getEntityManager()
    {
        return $this->entityManager;
    }

    /**
     * @param int $id user id
     *
     * @return Administrator|null
     */
    public function getAdministrator($id = 0)
    {
        $rowset = $this->select(['user' => (int) $id]);
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
            $this->delete(['user' => (int) $id]);
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
            $this->insert($data);
        } else {
            if ($this->getAdministrator($id)) {
                $this->update($data, ['user' => $id]);
            }
        }
        unset($id, $data);
        return $administrator;
    }
}
