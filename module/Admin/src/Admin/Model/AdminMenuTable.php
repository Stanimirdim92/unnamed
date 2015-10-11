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

final class AdminMenuTable extends AbstractModelTable
{
    /**
     * @method __construct
     *
     * @param \Zend\Db\Adapter\Adapter $adapter
     */
    public function __construct($adapter)
    {
        parent::__construct('adminmenu', 'AdminMenu', $adapter);
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
        $rowset = $this->tableGateway->select(['id' => (int) $id]);
        $rowset->buffer();
        if (!$rowset->current()) {
            throw new RuntimeException("Couldn't find admin menu");
        }
        return $rowset->current();
    }

    /**
     * Delete a adminmenu based on the provided id and language.
     *
     * @param int $id adminmenu id
     *
     * @return AdminMenu
     */
    public function deleteAdminMenu($id = 0)
    {
        if ($this->getAdminMenu($id)) {
            $this->tableGateway->delete(['id' => (int) $id]);
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
            $this->tableGateway->insert($data);
        } else {
            if ($this->getAdminMenu($id)) {
                $this->tableGateway->update($data, ['id' => $id]);
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
