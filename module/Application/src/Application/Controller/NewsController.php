<?php

/**
 * @copyright  2015 (c) Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 *
 * @version    0.0.14
 *
 * @link       TBA
 */

namespace Application\Controller;

final class NewsController extends IndexController
{
    /**
     * Get the contents for all the news or only one newspost.
     *
     * @return ViewModel
     */
    protected function postAction()
    {
        $this->getView()->setTemplate("application/news/post");
        $escaper = new \Zend\Escaper\Escaper('utf-8');
        $post = (string) $escaper->escapeUrl($this->getParam("post"));
        $content = $this->getTable("content");

        if (!empty($post)) {
            $new = $content->fetchList(false, ["title", "text", "date", "preview"], ["type" => 1, "menu" => 0, "titleLink" => $post, "language" => $this->language()], "AND", null, "date DESC")->getDataSource()->current();
            $this->getView()->new = $new;
            $this->initMetaTags($new);
        } else {
            $news = $content->fetchList(true, ["title", "titleLink", "text", "date", "preview"], ["type" => 1, "menu" => 0, "language" => $this->language()], "AND", null, "date DESC");
            $news->setCurrentPageNumber((int)$this->getParam('page', 1));
            $news->setItemCountPerPage(2); // must be set from db
            $this->getView()->news = $news;
        }
        return $this->getView();
    }
}
