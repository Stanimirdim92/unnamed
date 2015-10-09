<?php

/**
 * @copyright  2015 (c) Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 *
 * @version    0.0.16
 *
 * @link       TBA
 */

namespace Application\Controller;

final class NewsController extends IndexController
{
    /**
     * Get the contents for all the news.
     *
     * @return ViewModel
     */
    public function indexAction()
    {
        $this->getView()->setTemplate("application/news/index");

        $news = $this->getTable("content")->fetchList(true, ["title", "titleLink", "text", "date", "preview"], ["type" => 1, "menu" => 0, "language" => $this->language()], "AND", null, "date DESC");
        $news->setCurrentPageNumber((int)$this->getParam('page', 1));
        $news->setItemCountPerPage($this->systemSettings("posts", "news"));
        $this->getView()->news = $news;

        return $this->getView();
    }

    /**
     * Get the contents for one newspost.
     *
     * @return ViewModel
     */
    public function postAction()
    {
        $this->getView()->setTemplate("application/news/post");

        $escaper = new \Zend\Escaper\Escaper('utf-8');
        $post = (string) $escaper->escapeUrl($this->getParam("post"));
        $new = $this->getTable("content")->fetchList(false, ["title", "text", "date", "preview"], ["type" => 1, "menu" => 0, "titleLink" => $post, "language" => $this->language()], "AND", null, "date DESC");

        if (!empty($new)) {
            $new = $new->getDataSource()->current();
            $this->getView()->new = $new;
            $this->initMetaTags($new);
            return $this->getView();
        } else {
            return $this->setErrorCode(404);
        }
    }
}
