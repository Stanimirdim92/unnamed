<?php
namespace Application\Controller;

use Application\Controller\IndexController;

class MenuController extends IndexController
{
    public function onDispatch(\Zend\Mvc\MvcEvent $e)
    {
        parent::onDispatch($e);
    }

    /**
     * Get the contents for the menu/submenu
     *
     * @return mixed Object holding the menu text, name etc.
     */
    public function menuAction()
    {
        $param = (string) $this->getParam("title");

        if(empty($param))
        {
            $this->getResponse()->setStatusCode(404);
            $this->view->setTemplate('layout/error-layout');
            return $this->view;
        }

        $menu = $this->matchSEOMenu($param);

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
            throw new \Exception("Oops, an error has occurred.");
        }

        $this->view->contents = $contents;
        // $this->setMetaTags($contents);
        return $this->view;
    }

    /**
     * @param  null $param is the menu/controller name passed as string from the URL
     * @return array containting menu ids
     */
    private function matchSEOMenu($param = null)
    {
        $matches = $this->getTable("Menu")->fetchList(false, "caption = '{$param}' AND language='".$this->langTranslation."'");
        if (!$matches->current())
        {
            $param = str_replace(array("-","_"), array(" ","/"), $param);
            $matches = $this->getTable("Menu")->fetchList(false, "caption LIKE '%{$param}%' AND language='".$this->langTranslation."'");
        }

        if(count($matches) === 1)
        {
            $location = array("menu" => null, "submenu" => null);

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