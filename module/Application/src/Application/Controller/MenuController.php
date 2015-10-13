<?php

/**
 * @copyright  2015 (c) Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 *
 * @version    0.0.18
 *
 * @link       TBA
 */

namespace Application\Controller;

final class MenuController extends IndexController
{
    /**
     * Get the contents for the menu/submenu.
     *
     * @return ViewModel
     */
    protected function postAction()
    {
        $this->getView()->setTemplate("application/menu/post");
        $escaper = new \Zend\Escaper\Escaper('utf-8');

        $contents = $this->getTable("ContentTable");
        $contents->columns(["menu", "text", "id", "title", "titleLink", "preview"]);
        $contents->join("menu", "content.menu=menu.id", ["parent", "keywords", "description"], "inner");
        $contents->where(["menu.menulink" => (string) $escaper->escapeUrl($this->getParam("post")), "content.type" => 0, "content.language" => $this->language()], "AND");
        $contents->order("menu.parent ASC, menu.menuOrder ASC");
        $contents = $contents->fetch();

        if ($contents) {
            $this->initMetaTags($contents->getDataSource()->current());
            $this->getView()->contents = $contents;
        }

        return $this->getView();
    }
}
