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

final class AdminMenuTable
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
        $adminMenu = $this->queryBuilder();
        $adminMenu->select(["am"]);
        $adminMenu->from('Admin\Entity\AdminMenu', 'am');
        $adminMenu->where("am.id = :id");
        $adminMenu->setParameter(':id', (int) $id);
        $adminMenu = $adminMenu->getQuery()->getSingleResult();

        if (empty($adminMenu)) {
            throw new RuntimeException("Couldn't find admin menu");
        }

        return $adminMenu;
    }

    /**
     * Delete a adminmenu based on the provided id.
     *
     * @param int $id adminmenu id
     *
     * @return AdminMenu
     */
    public function deleteAdminMenu($id = 0)
    {
        if ($this->getAdminMenu($id)) {
            $del = $this->queryBuilder();
            $del->delete('Admin\Entity\AdminMenu', 'am');
            $del->where("am.id = :id");
            $del->setParameter(':id', (int) $id);

            return $del->getQuery()->execute();
        }
    }

    /**
     * Save or update menu based on the provided id and language.
     *
     * @param  AdminMenu $adminMenu
     *
     * @return AdminMenu
     */
    public function saveAdminMenu(AdminMenu $adminMenu)
    {
        $data = [
            'caption'     => (string) $adminMenu->getCaption(),
            'description' => (string) $adminMenu->getDescription(),
            'menuOrder'   => (int) $adminMenu->getMenuOrder(),
            'controller'  => (string) $adminMenu->getController(),
            'action'      => (string) $adminMenu->getAction(),
            'class'       => (string) $adminMenu->getClass(),
            'parent'      => (int) $adminMenu->getParent(),
        ];
        $id = (int)$adminMenu->getId();
        if (!$id) {
            $this->insert($data);
        } else {
            if ($this->getAdminMenu($id)) {
                $this->update($data, ['id' => $id]);
            }
        }
        unset($id, $data);
        return $adminMenu;
    }

    /**
     * duplicate a content.
     *
     * @param  int    $id
     *
     * @return AdminMenu
     */
    public function duplicate($id = 0)
    {
        $adminMenu = $this->getAdminMenu($id);
        $clone = $adminMenu->getCopy();
        $this->saveAdminMenu($clone);
        return $clone;
    }
}
