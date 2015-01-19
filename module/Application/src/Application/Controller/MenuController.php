<?php
namespace Application\Controller;

class MenuController extends \Application\Controller\IndexController
{
    public function onDispatch(\Zend\Mvc\MvcEvent $e)
    {
        parent::onDispatch($e);
    }

    /**
     * Get the contents for the menu/submenu
     *
     * @return Content
     */
    public function menuAction()
    {
        $title = (string) $this->getParam("title");

        if(empty($title))
        {
            $this->getResponse()->setStatusCode(404);
            $this->view->setTemplate('layout/error-layout');
            return $this->view;
        }

        $menu = $this->matchSEOMenu($title);

        if (!empty($menu["menu"]))
        {
            $contents = $this->getTable("Content")->fetchList(false, "menu='{$menu["menu"]}' AND language='".$this->langTranslation."'", "menuOrder ASC");
        }
        else if(!empty($menu["submenu"]))
        {
            $contents = $this->getTable("Content")->fetchList(false, "menu='{$menu["submenu"]}' AND language='".$this->langTranslation."'", "menuOrder ASC");
        }
        else
        {
            throw new \Exception($this->translation->OOPS_ERROR);
        }

        $this->view->contents = $contents;
        $this->setMetaTags($contents);
        return $this->view;
    }

    /**
     * @param  null $title is the menu/controller name passed as string from the URL
     * @return array containting menu/submenu ids
     */
    private function matchSEOMenu($title = null)
    {
        $matches = $this->getTable("Menu")->fetchList(false, "menulink = '{$title}' AND language='".$this->langTranslation."'");

        if(count($matches) === 1)
        {
            $location = array("menu" => 0, "submenu" => 0);

            if($matches->current()->getParent())
            {
                $location["menu"] = (int) $matches->current()->getId();
                $location["submenu"] = (int) $matches->current()->getParent();
            }
            $location["menu"] = (int) $matches->current()->getId();
            return $location;
        }
        return false;
    }
}
?>