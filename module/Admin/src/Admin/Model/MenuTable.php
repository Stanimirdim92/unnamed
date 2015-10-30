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
use Admin\Entity\Menu;

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
     * @return Admin\Entity\Menu
     */
    public function getEntityRepository()
    {
        return $this->entityManager->getRepository("Admin\Entity\Menu");
    }

    /**
     * @param int $id menu id
     * @param int $language user language
     *
     * @throws RuntimeException If menu is not found
     *
     * @return Menu
     */
    public function getMenu($id = 0, $language = 1)
    {
        $menu = $this->queryBuilder();
        $menu->select(["m"]);
        $menu->from('Admin\Entity\Menu', 'm');
        $menu->where("m.id = :id AND m.language = :language");
        $menu->setParameter(':id', (int) $id);
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
     * @param int $id menu id
     * @param int $language user language
     *
     * @return int
     */
    public function deleteMenu($id = 0, $language = 1)
    {
        if ($this->getMenu($id, $language)) {
            $del = $this->queryBuilder();
            $del->delete('Admin\Entity\Menu', 'm');
            $del->where("m.id = :id AND m.language = :language");
            $del->setParameter(':id', (int) $id);
            $del->setParameter(':language', (int) $language);

            return $del->getQuery()->execute();
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
     * @param int $id menu id
     * @param int $language user language
     * @param int $state 0 - deactivated, 1 - active
     */
    public function toggleActiveMenu($id = 0, $language = 1, $state = 0)
    {
        $menu = $this->getMenu($id, $language);

        if ($menu) {
            $menu->setActive($state);
            $this->saveMenu($menu);
        }
    }
}
