<?php
namespace Application\Controller;

use Application\ControllerIndexController;

class NewsController extends IndexController
{
    public function onDispatch(\Zend\Mvc\MvcEvent $e)
    {
        parent::onDispatch($e);
    }

    public function newsAction()
    {
        $post = $this->getParam("post");
        if($post)
        {
            try
            {
                $new = $this->getTable("Content")->fetchList(false, "type='1' AND title='{$post}' AND language='".$this->translation->language."'", "date DESC");
                if (!$new->current())
                {
                    $post = str_replace(array("-","_"),array(" ","/"), $post);
                    $new = $this->getTable("Content")->fetchList(false, "title LIKE '%{$post}%' AND language='".$this->translation->language."'", "date DESC");
                }

                if (count($new) === 0)
                {
                    throw new \Exception($this->translation->NEWS_NOT_FOUND);
                }
                $this->view->new = $new->current();
                $this->setMetaTags($new, "news");
            }
            catch(\Exception $e)
            {
                throw new \Exception($this->translation->NEWS_NOT_FOUND);
            }
        }
        else
        {
            $news = $this->getTable("content")->fetchList(true, "type='1' AND language='".$this->translation->language."'", "date DESC");
            $news->setCurrentPageNumber((int)$this->params('page', 1));
            $news->setItemCountPerPage(2);
            $this->setMetaTags($news, "news");
            $this->view->news = $news;
        }
        return $this->view;
    }
}
?>