<?php

/**
 * @copyright  2015 (c) Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 *
 * @version    0.0.15
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

        $contents = $this->getTable("Content")->fetchJoin(false, "menu", ["menu", "text", "id", "title", "titleLink", "preview"], ["parent", "keywords", "description"], "content.menu=menu.id", "inner", ["menu.menulink" => (string) $escaper->escapeUrl($this->getParam("post")), "content.type" => 0, "content.language" => $this->language()], null, "menu.parent ASC, menu.menuOrder ASC");

        if (!$contents) {
            return $this->setErrorCode(404);
        }

        $this->getView()->contents = $contents->getDataSource()->current();
        $this->initMetaTags($contents->getDataSource()->current());
        return $this->getView();
    }
}
