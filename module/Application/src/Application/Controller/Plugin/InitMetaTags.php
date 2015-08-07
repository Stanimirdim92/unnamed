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
 * @copyright  2015 Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 * @version    0.0.5
 * @link       TBA
 */

namespace Application\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;

class InitMetaTags extends AbstractPlugin
{
    /**
     * This function will generate all meta tags needed for SEO optimisation.
     *
     * @param array $content
     */
    protected function __invoke(array $content = [])
    {
        $serviceLocator = $this->getController()->getServiceLocator()->get('ViewHelperManager');

        /**
         * @var Zend\View\Helper\Placeholder\Container $placeholder
         */
        $placeholder = $serviceLocator->get("placeholder")->getContainer("customHead");

        /**
         * @var Zend\View\Helper\HeadMeta $vhm
         */
        $vhm = $serviceLocator->get("HeadMeta");

        $description = $keywords = $text = $preview = $title = $time = null;
        $request = $this->getController()->getRequest();

        if (!empty($content)) {
            $description = (isset($content["description"]) ? $content["description"] : "lorem ipsum dolar sit amet");
            $keywords = (isset($content["keywords"]) ? $content["keywords"] : "lorem, ipsum, dolar, sit, amet");
            $text = $content["text"];
            $preview = $content["preview"];
            $title = $content["title"];
        } else {
            $description = "lorem ipsum dolar sit amet";
            $keywords = "lorem, ipsum, dolar, sit, amet";
            $text = "lorem ipsum dolar sit amet";
            $preview = "";
            $title = "Unnamed";
        }

        $placeholder->append("\r\n<meta itemprop='name' content='Unnamed'>\r\n"); // must be sey from db
        $placeholder->append("<meta itemprop='description' content='".substr(strip_tags($text), 0, 150)."'>\r\n");
        $placeholder->append("<meta itemprop='title' content='".$title."'>\r\n");
        $placeholder->append("<meta itemprop='image' content='".$preview."'>\r\n");

        // $vhm->appendName('robots', 'index, follow');
        // $vhm->appendName('Googlebot', 'index, follow');
        // $vhm->appendName('revisit-after', '3 Days');
        $vhm->appendName('keywords', $keywords);
        $vhm->appendName('description', $description);
        $vhm->appendName('viewport', 'width=device-width, initial-scale=1.0');
        $vhm->appendName('generator', 'Unnamed');
        $vhm->appendName('apple-mobile-web-app-capable', 'yes');
        $vhm->appendName('application-name', 'Unnamed');
        $vhm->appendName('msapplication-TileColor', '#000000');
        $vhm->appendName('mobile-web-app-capable', 'yes');
        $vhm->appendName('HandheldFriendly', 'True');
        $vhm->appendName('MobileOptimized', '320');
        $vhm->appendName('apple-mobile-web-app-status-bar-style', 'black-translucent');
        $vhm->appendName('author', 'Stanimir Dimitrov - stanimirdim92@gmail.com');
        $vhm->appendProperty('og:image', $preview);
        // $vhm->appendProperty('article:published_time', date("Y-m-d H:i:s", time()));
        $vhm->appendProperty("og:title", $title);
        $vhm->appendProperty("og:description", $description);
        $vhm->appendProperty("og:type", 'article');
        $vhm->appendProperty("og:url", $request->getUri()->getHost().$request->getRequestUri());
        $vhm->appendHttpEquiv('cleartype', 'on');
        $vhm->appendHttpEquiv('x-dns-prefetch-control', 'on');

        /**
         * Other things that can be activated
         * Maybe allow full html tag input via a textarea. TODO: see if this is safe enough.
         */
        // <link href="https://plus.google.com/" rel="publisher" />
        // <meta name="google-site-verification" content="" />
    }
}
