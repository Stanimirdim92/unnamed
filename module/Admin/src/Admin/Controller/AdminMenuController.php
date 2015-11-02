<?php

/**
 * @copyright  2015 (c) Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 *
 * @version    0.0.21
 *
 * @link       TBA
 */

namespace Admin\Controller;

use Admin\Entity\AdminMenu;
use Admin\Form\AdminMenuForm;
use Zend\Mvc\MvcEvent;

final class AdminMenuController extends BaseController
{
    /**
     * @var AdminMenuForm
     */
    private $adminMenuForm;

    /**
     * @var \Admin\Model\AdminMenuTable
     */
    private $adminMenuTable;

    /**
     * @param AdminMenuForm $adminMenuForm
     */
    public function __construct(AdminMenuForm $adminMenuForm)
    {
        parent::__construct();

        $this->adminMenuForm = $adminMenuForm;
    }

    /**
     * @param MvcEvent $event
     */
    public function onDispatch(MvcEvent $event)
    {
        $this->addBreadcrumb(["reference"=>"/admin/adminmenu", "name"=>$this->translate("ADMIN_MENUS")]);
        $this->adminMenuTable = $this->getTable("Admin\\Model\\AdminMenuTable");

        parent::onDispatch($event);
    }

    /**
     * This action shows the list of all admin menus.
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function indexAction()
    {
        $this->getView()->setTemplate("admin/admin-menu/index");
        $menu = $this->adminMenuTable
                     ->getEntityRepository()
                     ->findAll();

        if (count($menu) > 0) {
            $menus = ["menus" => [], "submenus" => []];
            foreach ($menu as $submenu) {
                if ($submenu->getParent() > 0) {
                    $menus["submenus"][$submenu->getParent()][] = $submenu;
                } else {
                    $menus["menus"][$submenu->getId()] = $submenu;
                }
            }

            $this->getView()->menus = $menus["menus"];
            $this->getView()->submenus = $menus["submenus"];
        }

        return $this->getView();
    }

    /**
     * This action serves for adding a new admin menus.
     *
     * @return \Zend\View\Model\ViewModel
     */
    protected function addAction()
    {
        $this->getView()->setTemplate("admin/admin-menu/add");
        $this->initForm(null);
        $this->addBreadcrumb(["reference"=>"/admin/adminmenu/add", "name"=>$this->translate("ADD_ADMINMENU")]);

        return $this->getView();
    }

    /**
     * This action presents a edit form for AdminMenu with a given id.
     * Upon POST the form is processed and saved.
     *
     * @return \Zend\View\Model\ViewModel
     */
    protected function editAction()
    {
        $this->getView()->setTemplate("admin/admin-menu/edit");
        $adminMenu = $this->adminMenuTable->getAdminMenu((int) $this->getParam("id", 0));
        $this->getView()->adminMenu = $adminMenu;
        $this->addBreadcrumb(["reference"=>"/admin/adminmenu/edit/{$adminMenu->getId()}", "name"=>$this->translate("EDIT_ADMINMENU")." &laquo;".$adminMenu->getCaption()."&raquo;"]);
        $this->initForm($adminMenu);

        return $this->getView();
    }

    /**
     * This action deletes a admin menu with a provided id.
     */
    protected function deleteAction()
    {
        $this->adminMenuTable->deleteAdminMenu((int)$this->getParam("id", 0));
        $this->setLayoutMessages($this->translate("DELETE_ADMINMENU_SUCCESS"), "success");
    }

    /**
     * @return \Zend\View\Model\ViewModel
     */
    protected function detailAction()
    {
        $this->getView()->setTemplate("admin/admin-menu/detail");
        $adminmenu = $this->adminMenuTable->getAdminMenu((int)$this->getParam("id", 0), $this->language());
        $this->getView()->adminmenu = $adminmenu;
        $this->addBreadcrumb(["reference"=>"/admin/adminmenu/detail/".$adminmenu->getId()."", "name"=>"&laquo;". $adminmenu->getCaption()."&raquo; ".$this->translate("DETAILS")]);

        return $this->getView();
    }

    /**
     * This is common function used by add and edit actions (to avoid code duplication).
     *
     * @param AdminMenu $adminMenu
     */
    private function initForm(AdminMenu $adminMenu = null)
    {
        if (!$adminMenu instanceof AdminMenu) {
            $adminMenu = new AdminMenu([]);
        }

        $form = $this->adminMenuForm;
        $form->bind($adminMenu);
        $this->getView()->form = $form;

        if ($this->getRequest()->isPost()) {
            $form->setInputFilter($form->getInputFilter());
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $this->adminMenuTable->saveAdminMenu($adminMenu);
                return $this->setLayoutMessages("&laquo;".$adminMenu->getCaption()."&raquo; ".$this->translate("SAVE_SUCCESS"), 'success');
            }

            return $this->setLayoutMessages($form->getMessages(), 'error');
        }
    }
}
