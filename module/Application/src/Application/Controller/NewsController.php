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
 * @category   Application\News
 * @package    ZendPress
 * @author     Stanimir Dimitrov <stanimirdim92@gmail.com>
 * @copyright  2015 Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 * @version    0.03
 * @link       TBA
 */

namespace Application\Controller;

class NewsController extends \Application\Controller\IndexController
{
    public function onDispatch(\Zend\Mvc\MvcEvent $e)
    {
        parent::onDispatch($e);
    }

    public function newsAction()
    {
        $post = (string) $this->getParam("post", null);
        if(!empty($post))
        {
            $new = $this->getTable("Content")->fetchList(false, array(), "type='1' AND menu='0' AND titleLink='{$post}' AND language='".$this->langTranslation."'", "date DESC");
            if (count($new) == 0)
            {
                throw new \Exception($this->translation->NEWS_NOT_FOUND);
            }
            $this->view->new = $new->current();
            $this->setMetaTags($new, "news");
        }
        else
        {
            $news = $this->getTable("content")->fetchList(true, array(), "type='1' AND menu='0' AND language='".$this->langTranslation."'", "date DESC");
            $news->setCurrentPageNumber((int)$this->params('page', 1));
            $news->setItemCountPerPage(10);
            $this->view->news = $news;
        }
        return $this->view;
    }
}
?>