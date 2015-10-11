<?php

/**
 * @copyright  2015 (c) Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 *
 * @version    0.0.17
 *
 * @link       TBA
 */

namespace Admin\Model;

use Admin\Model\AbstractModelTable;
use Admin\Exception\RuntimeException;

final class MenuTable extends AbstractModelTable
{
    /**
     * @method __construct
     *
     * @param \Zend\Db\Adapter\Adapter $adapter
     */
    public function __construct($adapter)
    {
        parent::__construct('menu', 'Menu', $adapter);
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
        $rowset = $this->tableGateway->select(['id' => (int) $id, 'language' => (int) $language]);
        $rowset->buffer();
        if (!$rowset->current()) {
            throw new RuntimeException("Couldn't find menu");
        }
        return $rowset->current();
    }

    /**
     * Delete a menu based on the provided id and language.
     *
     * @param int $id menu id
     * @param int $language user language
     *
     * @return Menu
     */
    public function deleteMenu($id = 0, $language = 1)
    {
        if ($this->getMenu($id, $language)) {
            $this->tableGateway->delete(['id' => (int) $id, "language" => (int) $language]);
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
            $this->tableGateway->insert($data);
        } else {
            if ($this->getMenu($id, $language)) {
                $this->tableGateway->update($data, ['id' => $id, 'language' => $language]);
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
            $this->tableGateway->update(["active" => (int) $state], ['id' => (int) $id]);
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
