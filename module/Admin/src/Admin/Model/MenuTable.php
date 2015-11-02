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
use Admin\Entity\Menu;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as PaginatorAdapter;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use Zend\Paginator\Paginator as ZendPaginator;

final class MenuTable
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
     * @return Admin\Entity\Menu
     */
    public function getEntityRepository()
    {
        return $this->entityManager->getRepository("Admin\Entity\Menu");
    }

    /**
     * @param int $menuId menu id
     * @param int $language user language
     *
     * @throws RuntimeException If menu is not found
     *
     * @return Menu
     */
    public function getMenu($menuId = 0, $language = 1)
    {
        $menu = $this->queryBuilder();
        $menu->select(["m"]);
        $menu->from('Admin\Entity\Menu', 'm');
        $menu->where("m.id = :id AND m.language = :language");
        $menu->setParameter(':id', (int) $menuId);
        $menu->setParameter(':language', (int) $language);
        $menu = $menu->getQuery()->getSingleResult();

        if (empty($menu)) {
            throw new RuntimeException("Couldn't find menu");
        }

        return $menu;
    }

    /**
     * Delete a menu based on the provided id and language.
     *
     * @param int $menuId menu id
     * @param int $language user language
     */
    public function deleteMenu($menuId = 0, $language = 1)
    {
        $menu = $this->getMenu($menuId, $language);
        if ($menu) {
            $this->entityManager->remove($menu);
            $this->entityManager->flush();
        }
    }

    /**
     * @param Menu $menu
     *
     * @return Menu
     */
    public function saveMenu(Menu $menu)
    {
        $this->entityManager->persist($menu);
        $this->entityManager->flush();

        return $menu;
    }

    /**
     * This method can disable or enable menus.
     *
     * @param int $menuId menu id
     * @param int $language user language
     * @param int $state 0 - deactivated, 1 - active
     */
    public function toggleActiveMenu($menuId = 0, $language = 1, $state = 0)
    {
        $menu = $this->getMenu($menuId, $language);

        if ($menu) {
            $menu->setActive((int) $state);
            $this->saveMenu($menu);
        }
    }
}
