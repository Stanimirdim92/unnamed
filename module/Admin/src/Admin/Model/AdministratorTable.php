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

use Admin\Exception\RuntimeException;
use Doctrine\ORM\EntityManager;
use Admin\Entity\Administrator;
use Doctrine\ORM\Tools\Pagination\Paginator;

final class AdministratorTable
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @param EntityManager $entityManager
     */
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
     * @param Query|QueryBuilder $query               A Doctrine ORM query or query builder.
     * @param boolean            $fetchJoinCollection Whether the query joins a collection (true by default).
     *
     * @return Paginator
     */
    public function preparePagination($query, $fetchJoinCollection = true)
    {
        return new Paginator($query, $fetchJoinCollection);
    }

    /**
     * @return Admin\Entity\Administrator
     */
    public function getEntityRepository()
    {
        return $this->entityManager->getRepository("Admin\Entity\Administrator");
    }

    /**
     * @param int $id user id
     *
     * @return Administrator
     */
    public function getAdministrator($id = 0)
    {
        $administrator = $this->getEntityRepository()->find($id);

        if (empty($administrator)) {
            throw new RuntimeException("Couldn't find administrator");
        }

        return $administrator;
    }

    /**
     * Delete a administrator based on the provided user id.
     *
     * @param int $id user id
     */
    public function deleteAdministrator($id = 0)
    {
        $administrator = $this->getAdministrator($id);
        if ($administrator) {
            $this->entityManager->remove($administrator);
            $this->entityManager->flush();
        }
    }

    /**
     * Save or update administrator based on the provided id.
     *
     * @param Administrator $administrator
     *
     * @return Administrator
     */
    public function saveAdministrator(Administrator $administrator)
    {
        $this->entityManager->persist($administrator);
        $this->entityManager->flush();

        return $administrator;
    }
}
