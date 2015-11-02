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
use Admin\Entity\AdminMenu;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as PaginatorAdapter;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use Zend\Paginator\Paginator as ZendPaginator;

final class AdminMenuTable
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
     * @return Admin\Entity\AdminMenu
     */
    public function getEntityRepository()
    {
        return $this->entityManager->getRepository("Admin\Entity\AdminMenu");
    }

    /**
     * @param int $adminMenuId adminmenu id
     *
     * @throws RuntimeException If adminmenu is not found
     *
     * @return AdminMenu
     */
    public function getAdminMenu($adminMenuId = 0)
    {
        $adminMenu = $this->getEntityRepository()->find($adminMenuId);

        if (empty($adminMenu)) {
            throw new RuntimeException("Couldn't find admin menu");
        }

        return $adminMenu;
    }

    /**
     * Delete a adminmenu based on the provided id.
     *
     * @param int $adminMenuId admin menu id
     *
     * @return AdminMenu
     */
    public function deleteAdminMenu($adminMenuId = 0)
    {
        $adminMenu = $this->getAdminMenu($adminMenuId);

        if ($adminMenu) {
            $this->entityManager->remove($adminMenu);
            $this->entityManager->flush();
        }
    }

    /**
     * Save or update menu based on the provided id and language.
     *
     * @param AdminMenu $adminMenu
     *
     * @return AdminMenu
     */
    public function saveAdminMenu(AdminMenu $adminMenu)
    {
        $this->entityManager->persist($adminMenu);
        $this->entityManager->flush();

        return $adminMenu;
    }
}
