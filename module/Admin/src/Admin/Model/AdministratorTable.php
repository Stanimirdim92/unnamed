<?php

/**
 * @copyright  2015 (c) Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 *
 * @version    0.0.21
 *
 * @link       TBA
 */

namespace Admin\Model;

use Admin\Exception\RuntimeException;
use Doctrine\ORM\EntityManager;
use Admin\Entity\Administrator;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as PaginatorAdapter;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use Zend\Paginator\Paginator as ZendPaginator;

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
        return new ZendPaginator(new PaginatorAdapter(new ORMPaginator($query, $fetchJoinCollection)));
    }

    /**
     * @return Admin\Entity\Administrator
     */
    public function getEntityRepository()
    {
        return $this->entityManager->getRepository("Admin\Entity\Administrator");
    }

    /**
     * @param int $adminId user id
     *
     * @return Administrator
     */
    public function getAdministrator($adminId = 0)
    {
        $administrator = $this->getEntityRepository()->findBy(["user" => $adminId]);

        if (empty($administrator)) {
            throw new RuntimeException("Couldn't find administrator");
        }

        return $administrator[0];
    }

    /**
     * Delete a administrator based on the provided user id.
     *
     * @param int $adminId user id
     */
    public function deleteAdministrator($adminId = 0)
    {
        $administrator = $this->getAdministrator($adminId);

        if ($administrator) {
            $this->entityManager->remove($administrator[0]);
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
