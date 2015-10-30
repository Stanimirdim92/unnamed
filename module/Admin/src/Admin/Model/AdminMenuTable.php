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
use Admin\Entity\AdminMenu;

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
     * @return Admin\Entity\AdminMenu
     */
    public function getEntityRepository()
    {
        return $this->entityManager->getRepository("Admin\Entity\AdminMenu");
    }

    /**
     * @param int $id adminmenu id
     *
     * @throws RuntimeException If adminmenu is not found
     *
     * @return AdminMenu
     */
    public function getAdminMenu($id = 0)
    {
        $adminMenu = $this->getEntityRepository()->find($id);

        if (empty($adminMenu)) {
            throw new RuntimeException("Couldn't find admin menu");
        }

        return $adminMenu;
    }

    /**
     * Delete a adminmenu based on the provided id.
     *
     * @param int $id admin menu id
     *
     * @return AdminMenu
     */
    public function deleteAdminMenu($id = 0)
    {
        $adminMenu = $this->getAdminMenu($id);

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
