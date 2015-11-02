<?php

/**
 * @copyright  2015 (c) Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 *
 * @version    0.0.21
 *
 * @link       TBA
 */

namespace Application\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\View\Helper\Placeholder\Container as Placeholder;
use Zend\View\Helper\HeadMeta;
use Zend\Http\PhpEnvironment\Request;
use Application\Controller\Plugin\SystemSettings;

final class InitMetaTags extends AbstractPlugin
{
    /**
     * @var Placeholder
     */
    private $placeholder;

    /**
     * @var HeadMeta
     */
    private $headMeta;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var SystemSettings
     */
    private $settings;

    /**
     * @param Placeholder $placeholder
     * @param HeadMeta $headMeta
     * @param Request $request
     */
    public function __construct(
        Placeholder $placeholder = null,
        HeadMeta $headMeta = null,
        Request $request = null,
        SystemSettings $settings = null
    ) {
        $this->placeholder = $placeholder;
        $this->headMeta = $headMeta;
        $this->request = $request;
        $this->settings = $settings;
    }

    /**
     * This function will generate all meta tags needed for SEO optimisation.
     *
     * @param array $content
     */
    public function __invoke(array $content = [])
    {
        $description = (!empty($content["description"]) ? $content["description"] : $this->settings->__invoke("general", "site_description"));
        $keywords = (!empty($content["keywords"]) ? $content["keywords"] : $this->settings->__invoke("general", "site_keywords"));
        $text = (!empty($content["text"]) ? $content["text"] : $this->settings->__invoke("general", "site_text"));
        $preview = (!empty($content["preview"]) ? $content["preview"] : "");
        $title = (!empty($content["title"]) ? $content["title"] : $this->settings->__invoke("general", "site_name"));

        $this->placeholder->append("\r\n<meta itemprop='name' content='{$this->settings->__invoke("general", "site_name")}'>\r\n"); // must be set from db
        $this->placeholder->append("<meta itemprop='description' content='".substr(strip_tags($text), 0, 150)."'>\r\n");
        $this->placeholder->append("<meta itemprop='title' content='".$title."'>\r\n");
        $this->placeholder->append("<meta itemprop='image' content='".$preview."'>\r\n");

        $this->headMeta->appendName('keywords', $keywords);
        $this->headMeta->appendName('description', $description);
        $this->headMeta->appendName('viewport', 'width=device-width, initial-scale=1.0');
        $this->headMeta->appendName('generator', $this->settings->__invoke("general", "site_name"));
        $this->headMeta->appendName('apple-mobile-web-app-capable', 'yes');
        $this->headMeta->appendName('application-name', $this->settings->__invoke("general", "site_name"));
        $this->headMeta->appendName('mobile-web-app-capable', 'yes');
        $this->headMeta->appendName('HandheldFriendly', 'True');
        $this->headMeta->appendName('MobileOptimized', '320');
        $this->headMeta->appendName('apple-mobile-web-app-status-bar-style', 'black-translucent');
        $this->headMeta->appendName('author', 'Stanimir Dimitrov - stanimirdim92@gmail.com');
        $this->headMeta->appendName('twitter:card', 'summary');
        $this->headMeta->appendName('twitter:site', '@'.$this->settings->__invoke("general", "site_name"));
        $this->headMeta->appendName('twitter:title', substr(strip_tags($title), 0, 70)); // max 70 chars
        $this->headMeta->appendName('twitter:description', substr(strip_tags($text), 0, 200));
        $this->headMeta->appendName('twitter:image', $preview); // max 1MB

        $this->headMeta->appendProperty('og:image', $preview);
        $this->headMeta->appendProperty("og:title", $title);
        $this->headMeta->appendProperty("og:description", $description);
        $this->headMeta->appendProperty("og:type", 'article');
        $this->headMeta->appendProperty("og:url", $this->request->getUri()->getHost().$this->request->getRequestUri());
    }
}
