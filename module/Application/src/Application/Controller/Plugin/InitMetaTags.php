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
 * @version    0.0.13
 * @link       TBA
 */

namespace Application\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\View\Helper\Placeholder\Container as Placeholder;
use Zend\View\Helper\HeadMeta;
use Zend\Http\PhpEnvironment\Request;

final class InitMetaTags extends AbstractPlugin
{
    /**
     * @var Placeholder
     */
    private $placeholder = null;

    /**
     * @var HeadMeta
     */
    private $headMeta = null;

    /**
     * @var Request
     */
    private $request = null;

    /**
     * @param Placeholder $placeholder
     * @param HeadMeta $headMeta
     * @param Request $request
     */
    public function __construct(Placeholder $placeholder = null, HeadMeta $headMeta = null, Request $request = null)
    {
        $this->placeholder = $placeholder;
        $this->headMeta = $headMeta;
        $this->request = $request;
    }

    /**
     * This function will generate all meta tags needed for SEO optimisation.
     *
     * @param array $content
     */
    public function __invoke(array $content = [])
    {
        $description = (!empty($content["description"]) ? $content["description"] : "lorem ipsum dolar sit amet");
        $keywords = (!empty($content["keywords"]) ? $content["keywords"] : "lorem, ipsum, dolar, sit, amet");
        $text = (!empty($content["text"]) ? $content["text"] : "lorem ipsum dolar sit amet");
        $preview = (!empty($content["preview"]) ? $content["preview"] : "");
        $title = (!empty($content["title"]) ? $content["title"] : "");

        $this->placeholder->append("\r\n<meta itemprop='name' content='Unnamed'>\r\n"); // must be sey from db
        $this->placeholder->append("<meta itemprop='description' content='".substr(strip_tags($text), 0, 150)."'>\r\n");
        $this->placeholder->append("<meta itemprop='title' content='".$title."'>\r\n");
        $this->placeholder->append("<meta itemprop='image' content='".$preview."'>\r\n");

        // $this->headMeta->appendName('robots', 'index, follow');
        // $this->headMeta->appendName('Googlebot', 'index, follow');
        // $this->headMeta->appendName('revisit-after', '3 Days');
        $this->headMeta->appendName('keywords', $keywords);
        $this->headMeta->appendName('description', $description);
        $this->headMeta->appendName('viewport', 'width=device-width, initial-scale=1.0');
        $this->headMeta->appendName('generator', 'Unnamed');
        $this->headMeta->appendName('apple-mobile-web-app-capable', 'yes');
        $this->headMeta->appendName('application-name', 'Unnamed');
        $this->headMeta->appendName('msapplication-TileColor', '#000000');
        $this->headMeta->appendName('mobile-web-app-capable', 'yes');
        $this->headMeta->appendName('HandheldFriendly', 'True');
        $this->headMeta->appendName('MobileOptimized', '320');
        $this->headMeta->appendName('apple-mobile-web-app-status-bar-style', 'black-translucent');
        $this->headMeta->appendName('author', 'Stanimir Dimitrov - stanimirdim92@gmail.com');
        $this->headMeta->appendProperty('og:image', $preview);
        // $this->headMeta->appendProperty('article:published_time', date("Y-m-d H:i:s", time()));
        $this->headMeta->appendProperty("og:title", $title);
        $this->headMeta->appendProperty("og:description", $description);
        $this->headMeta->appendProperty("og:type", 'article');
        $this->headMeta->appendProperty("og:url", $this->request->getUri()->getHost().$this->request->getRequestUri());

        /**
         * Other things that can be activated
         * Maybe allow full html tag input via a textarea. TODO: see if this is safe enough.
         */
        // <link href="https://plus.google.com/" rel="publisher" />
        // <meta name="google-site-verification" content="" />
    }
}
