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
use Admin\Entity\User;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as PaginatorAdapter;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use Zend\Paginator\Paginator as ZendPaginator;

final class UserTable
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
     * @return Admin\Entity\User
     */
    public function getEntityRepository()
    {
        return $this->entityManager->getRepository("Admin\Entity\User");
    }

    /**
     * @param int $userId user id
     *
     * @return array
     */
    public function getUser($userId = 0)
    {
        $user = $this->getEntityRepository()->find($userId);

        if (empty($user)) {
            throw new RuntimeException("Couldn't find user");
        }

        return $user[0];
    }

    /**
     * This method can disable or enable user accounts.
     *
     * @param int $userId user id
     * @param int $state 0 - enabled, 1 - disabled
     */
    public function toggleUserState($userId = 0, $state = 0)
    {
        $user = $this->getUser($userId);
        if ($user) {
            $user->setDisabled((int) $state);
            $user->setAdmin(0); // doesn't matter the $state. Remove user admin rights.
            $this->saveUser($user);
        }
    }

    /**
     * Update user based on the provided id.
     *
     * @param User $user
     *
     * @return User
     */
    public function saveUser(User $user)
    {
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }
}
