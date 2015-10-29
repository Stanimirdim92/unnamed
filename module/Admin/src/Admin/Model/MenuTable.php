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

final class MenuTable
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
     * Save or update menu based on the provided id and language.
     *
     * @param  Menu $menu
     *
     * @return Menu
     */
    public function saveMenu(Menu $menu)
    {
        $data = [
            'caption'      => (string) $menu->getCaption(),
            'menuOrder'    => (int) $menu->getMenuOrder(),
            'language'     => (int) $menu->getLanguage(),
            'parent'       => (int) $menu->getParent(),
            'keywords'     => (string) $menu->getKeywords(),
            'description'  => (string) $menu->getDescription(),
            'menutype'     => (int) $menu->getMenuType(),
            'footercolumn' => (int) $menu->getFooterColumn(),
            'menulink'     => (string) $menu->getMenuLink(),
            'active'       => (int) $menu->getActive(),
            'class'        => (string) $menu->getClass(),
        ];
        $id = (int) $menu->getId();
        $language = (int) $menu->getLanguage();
        if (!$id) {
            $this->insert($data);
        } else {
            if ($this->getMenu($id, $language)) {
                $this->update($data, ['id' => $id, 'language' => $language]);
            }
        }
        unset($id, $language, $data);

        return $menu;
    }

    /**
     * This method can disable or enable menus.
     *
     * @param int $id menu id
     * @param int $state 0 - deactivated, 1 - active
     */
    public function toggleActiveMenu($id = 0, $state = 0)
    {
        if ($this->getMenu($id)) {
            $this->update(["active" => (int) $state], ['id' => (int) $id]);
        }
    }

    /**
     * Duplicate menu.
     *
     * @param int $id
     * @param int $language
     *
     * @return Menu
     */
    public function duplicate($id = 0, $language = 1)
    {
        $menu = $this->getMenu($id, $language);
        $clone = $menu->getCopy();
        $this->saveMenu($clone);

        return $clone;
    }
}
