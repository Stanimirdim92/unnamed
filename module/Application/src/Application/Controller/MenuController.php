<?php
namespace Application\Controller;

class MenuController extends \Application\Controller\IndexController
{
    public function onDispatch(\Zend\Mvc\MvcEvent $e)
    {
        parent::onDispatch($e);
    }

    /**
     * Get the contents for the menu/submenu. First we check for parent menu and if not found we call the submenu
     *
     * @throws Exception If no menu is found
     * @return Content
     */
    public function menuAction()
    {
        $title = (string) $this->getParam("title");

        if(empty($title))
        {
            $this->setErrorCode();
        }

        $contents = $this->getTable("Content")->fetchJoin(false, "menu", "content.menu=menu.id", "menu.menulink = '{$title}' AND (type='0') AND (content.menu != '0') AND (content.language='".$this->langTranslation."')", "menu.parent ASC, menu.menuOrder ASC, content.date DESC");
        
        // for now if there is no content for the menu we wil lshow 404 page, but this might change
        if (!count($contents))
        {
            $this->setErrorCode();
        }

        $this->view->contents = $contents;
        // $this->setMetaTags($contents);
        return $this->view;
    }
}
?>