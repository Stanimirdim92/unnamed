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
use Doctrine\ORM\EntityManager;
use Application\Entity\ResetPassword;

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
        return $this->entityManager->getRepository("Application\Entity\ResetPassword");
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
        $resetpw = $this->queryBuilder();
        $resetpw->select(["r"]);
        $resetpw->from('Application\Entity\ResetPassword', 'r');
        $resetpw->where("r.id = :id AND r.user = :user");
        $resetpw->setParameter(':id', (int) $id);
        $resetpw->setParameter(':user', (int) $user);
        $resetpw = $resetpw->getQuery()->getSingleResult();

        if (empty($resetpw)) {
            throw new RuntimeException("Couldn't find record");
        }

        return $resetpw;
    }

    /**
     * Save or update password based on the provided id.
     *
     * @param ResetPassword $resetpassword
     *
     * @return ResetPassword
     */
    public function saveResetPassword(ResetPassword $resetpw)
    {
        $this->entityManager->persist($resetpw);
        $this->entityManager->flush();

        return $resetpw;
    }
}
