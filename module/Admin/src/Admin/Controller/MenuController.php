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

use Admin\Entity\Menu;
use Admin\Form\MenuForm;
use Zend\Mvc\MvcEvent;

final class MenuController extends BaseController
{
    /**
     * @var MenuForm
     */
    private $menuForm;

    /**
     * @var Menu
     */
    private $menuTable;

    /**
     * @param MenuForm $menuForm
     */
    public function __construct(MenuForm $menuForm)
    {
        parent::__construct();
        $this->menuForm = $menuForm;
    }

    /**
     * @param MvcEvent $event
     */
    public function onDispatch(MvcEvent $event)
    {
        $this->addBreadcrumb(["reference"=>"/admin/menu", "name"=>$this->translate("MENUS")]);
        $this->menuTable = $this->getTable("Admin\\Model\\MenuTable");

        parent::onDispatch($event);
    }

    /**
     * Initialize menus and their submenus. 1 query to rule them all!
     *
     * @return string
     */
    private function showMenus()
    {
        $menu = $this->menuTable
                     ->getEntityRepository()
                     ->findBy(["language" => $this->language()], ['parent' => "DESC"]);

        $menus = ['menus' => [], 'submenus' => []];
        if (count($menu) > 0) {

            foreach ($menu as $submenus) {
                $menus['menus'][$submenus->getId()] = $submenus;
                $menus['submenus'][$submenus->getParent()][] = $submenus->getId();
            }

        }

        return $this->getMenus(0, $menus);
    }

    /**
     * Builds menu HTML.
     *
     * @method getMenus
     *
     * @param int $parent
     * @param array $menu
     *
     * @return string generated html code
     */
    private function getMenus($parent = 0, array $menu = [])
    {
        $output = "";
        if (isset($menu["submenus"][$parent])) {
            $escaper = new \Zend\Escaper\Escaper('utf-8');
            foreach ($menu['submenus'][$parent] as $id) {
                $output .= "<ul class='table-row'>";
                $output .= "<li class='table-cell flex-2'>{$menu['menus'][$id]->getCaption()}</li>";
                $output .= "<li class='table-cell flex-b'><a title='{$this->translate('DETAILS')}' hreflang='{$this->language("languageName")}' itemprop='url' href='/admin/menu/detail/{$escaper->escapeUrl($menu['menus'][$id]->getId())}' class='btn btn-sm blue'><i class='fa fa-info'></i></a></li>";
                $output .= "<li class='table-cell flex-b'><a title='{$this->translate('EDIT')}' hreflang='{$this->language("languageName")}' itemprop='url' href='/admin/menu/edit/{$escaper->escapeUrl($menu['menus'][$id]->getId())}' class='btn btn-sm orange'><i class='fa fa-pencil'></i></a></li>";
                if ($menu['menus'][$id]->isActive() == 0) {
                    $output .= "<li class='table-cell flex-b'><a title='{$this->translate('DEACTIVATED')}' hreflang='{$this->language("languageName")}' itemprop='url' href='/admin/menu/activate/{$escaper->escapeUrl($menu['menus'][$id]->getId())}' class='btn btn-sm deactivated'><i class='fa fa-minus-square-o'></i></a></li>";
                } else {
                    $output .= "<li class='table-cell flex-b'><a title='{$this->translate('ACTIVE')}' hreflang='{$this->language("languageName")}' itemprop='url' href='/admin/menu/deactivate/{$escaper->escapeUrl($menu['menus'][$id]->getId())}' class='btn btn-sm active'><i class='fa fa fa-check-square-o'></i></a></li>";
                }
                $output .= "
                <li class='table-cell flex-b'>
                    <button role='button' aria-pressed='false' aria-label='{$this->translate("DELETE")}' id='{$menu['menus'][$id]->getId()}' type='button' class='btn btn-sm delete dialog_delete' title='{$this->translate("DELETE")}'><i class='fa fa-trash-o'></i></button>
                        <div role='alertdialog' aria-labelledby='dialog{$menu['menus'][$id]->getId()}Title' class='delete_{$menu['menus'][$id]->getId()} dialog_hide'>
                           <p id='dialog{$menu['menus'][$id]->getId()}Title'>{$this->translate("DELETE_CONFIRM_TEXT")} &laquo;{$menu['menus'][$id]->getCaption()}&raquo;</p>
                            <ul>
                                <li>
                                    <a class='btn delete' href='/admin/menu/delete/{$escaper->escapeUrl($menu['menus'][$id]->getId())}'><i class='fa fa-trash-o'></i> {$this->translate("DELETE")}</a>
                                </li>
                                <li>
                                    <button role='button' aria-pressed='false' aria-label='{$this->translate("CANCEL")}' class='btn btn-default cancel'><i class='fa fa-times'></i> {$this->translate("CANCEL")}</button>
                                </li>
                            </ul>
                        </div>
                </li>";

                $output .= "</ul>";
                $output .= $this->getMenus($id, $menu);
            }
        }

        return $output;
    }

    /**
     * This action shows the list with all menus.
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function indexAction()
    {
        $this->getView()->setTemplate("admin/menu/index");

        $this->getView()->menus = $this->showMenus();

        return $this->getView();
    }

    /**
     * This action serves for adding a new menu
     *
     * @return \Zend\View\Model\ViewModel
     */
    protected function addAction()
    {
        $this->getView()->setTemplate("admin/menu/add");
        $this->initForm(null);
        $this->addBreadcrumb(["reference"=>"/admin/menu/add", "name"=>$this->translate("ADD_NEW_MENU")]);

        return $this->getView();
    }

    /**
     * This action presents a edit form for Menu object with a given id.
     * Upon POST the form is processed and saved.
     *
     * @return \Zend\View\Model\ViewModel
     */
    protected function editAction()
    {
        $this->getView()->setTemplate("admin/menu/edit");
        $menu = $this->menuTable->getMenu((int)$this->getParam("id", 0), $this->language());
        $this->addBreadcrumb(["reference"=>"/admin/menu/edit/{$menu->getId()}", "name"=> $this->translate("EDIT_MENU")." &laquo;".$menu->getCaption()."&raquo;"]);
        $this->initForm($menu);

        return $this->getView();
    }

    protected function deactivateAction()
    {
        $this->menuTable->toggleActiveMenu((int)$this->getParam("id", 0), $this->language(), 0);
        $this->setLayoutMessages($this->translate("MENU_DISABLE_SUCCESS"), "success");
        $this->redirect()->toUrl("/admin/menu");
    }

    protected function activateAction()
    {
        $this->menuTable->toggleActiveMenu((int)$this->getParam("id", 0), $this->language(), 1);
        $this->setLayoutMessages($this->translate("MENU_ENABLE_SUCCESS"), "success");
        $this->redirect()->toUrl("/admin/menu");
    }

    /**
     * This action deletes a menu object with a provided id and language.
     */
    protected function deleteAction()
    {
        $this->menuTable->deleteMenu((int)$this->getParam("id", 0), $this->language());
        $this->setLayoutMessages($this->translate("DELETE_MENU_SUCCESS"), "success");
    }

    /**
     * This action shows menu details from the provided id and session language.
     *
     * @return \Zend\View\Model\ViewModel
     */
    protected function detailAction()
    {
        $this->getView()->setTemplate("admin/menu/detail");
        $menu = $this->menuTable->getMenu((int)$this->getParam("id", 0), $this->language());
        $this->getView()->menuDetail = $menu;
        $this->addBreadcrumb(["reference"=>"/admin/menu/detail/".$menu->getId()."", "name"=>"&laquo;". $menu->getCaption()."&raquo; ".$this->translate("DETAILS")]);

        return $this->getView();
    }

    /**
     * This is common function used by add and edit actions (to avoid code duplication).
     *
     * @param Menu $menu
     */
    private function initForm(Menu $menu)
    {
        if (!$menu instanceof Menu) {
            $menu = new Menu([]);
        }

        $form = $this->menuForm;
        $form->bind($menu);
        $this->getView()->form = $form;
        $this->getView()->editMenu = $menu;

        if ($this->getRequest()->isPost()) {
            $form->setInputFilter($form->getInputFilter());
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $this->menuTable->saveMenu($menu);
                return $this->setLayoutMessages("&laquo;".$menu->getCaption()."&raquo; ".$this->translate("SAVE_SUCCESS"), 'success');
            }

            return $this->setLayoutMessages($form->getMessages(), 'error');
        }
    }
}
