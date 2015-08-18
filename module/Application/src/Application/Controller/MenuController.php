<?php
/**
 * MIT License
 * ===========
 *
 * Copyright (c) 2015 Stanimir Dimitrov <stanimirdim92@gmail.com>
 *
 * Permission is hereby granted, free of charge, to any person obtaining
 * a copy of this software and associated documentation files (the
 * "Software"), to deal in the Software without restriction, including
 * without limitation the rights to use, copy, modify, merge, publish,
 * distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to
 * the following conditions:
 *
 * The above copyright notice and this permission notice shall be included
 * in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS
 * OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
 * IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY
 * CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT,
 * TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE
 * SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 *
 * @author     Stanimir Dimitrov <stanimirdim92@gmail.com>
 * @copyright  2015 (c) Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 * @version    0.0.7
 * @link       TBA
 */

namespace Application\Controller;

final class MenuController extends IndexController
{
    /**
     * @param MvcEvent $e
     */
    public function onDispatch(\Zend\Mvc\MvcEvent $e)
    {
        parent::onDispatch($e);
    }

    /**
     * Get the contents for the menu/submenu.
     *
     * @return Admin\Model\Content
     */
    protected function titleAction()
    {
        $this->view->setTemplate("application/menu/title");
        $escaper = new \Zend\Escaper\Escaper('utf-8');

        $contents = $this->getTable("Content")->fetchJoin(false, "menu", ["menu", "text", "id", "title", "titleLink", "preview"], ["parent", "keywords", "description"], "content.menu=menu.id", "inner", ["menu.menulink" => (string) $escaper->escapeUrl($this->getParam("title")), "content.type" => 0, "content.language" => $this->language()], null, "menu.parent ASC, menu.menuOrder ASC");

        if (!$contents) {
            return $this->setErrorCode(404);
        }

        $this->view->contents = $contents->getDataSource()->current();
        $this->initMetaTags($contents->getDataSource()->current());
        return $this->view;
    }
}
