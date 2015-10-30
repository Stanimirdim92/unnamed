<?php

/**
 * @copyright  2015 (c) Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 *
 * @version    0.0.18
 *
 * @link       TBA
 */

namespace Application\Model;

use Application\Exception\RuntimeException;

final class ResetPasswordTable
{
    /**
     * @var Doctrine\ORM\EntityManager
     */
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @return Doctrine\ORM\QueryBuilder
     */
    public function queryBuilder()
    {
        return $this->entityManager->createQueryBuilder();
    }

    /**
     * @return Admin\Entity\ResetPassword
     */
    public function getEntityRepository()
    {
        return $this->entityManager->getRepository("Admin\Entity\ResetPassword");
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
        $rowset = $this->select(['id' => (int) $id, 'user' => (int) $user]);
        if (!$rowset->current()) {
            throw new RuntimeException("Couldn't find row");
        }
        return $rowset->current();

         $menu = $this->queryBuilder();
        $menu->select(["m"]);
        $menu->from('Admin\Entity\Menu', 'm');
        $menu->where("m.id = :id AND m.language = :language");
        $menu->setParameter(':id', (int) $id);
        $menu->setParameter(':language', (int) $language);
        $menu = $menu->getQuery()->getSingleResult();

        if (empty($menu)) {
            throw new RuntimeException("Couldn't find row");
        }

        return $menu;
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
            $this->insert($data);
        } else {
            if ($this->getResetPassword($id, $user)) {
                $this->update($data, ['id' => (int) $id, 'user' => (int) $user]);
            }
        }
        unset($id, $user, $data);
        return $resetpw;
    }
}
