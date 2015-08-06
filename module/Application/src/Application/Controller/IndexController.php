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
 * CLAIM, DAMAGES OR OTHER LIABILITY, WHE`HER IN AN ACTION OF CONTRACT,
 * TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE
 * SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 *
 * @author     Stanimir Dimitrov <stanimirdim92@gmail.com>
 * @copyright  2015 (c) Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 * @version    0.0.4
 * @link       TBA
 */

namespace Application\Controller;

use Zend\Session\Container;
use Zend\Authentication\AuthenticationService;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Custom\Error\AuthorizationException;

class IndexController extends AbstractActionController
{
    /**
     * @var Zend\View\Model\ViewModel $view creates instance to view model
     */
    protected $view = null;

    /**
     * @var Zend\Session\Container $translation holds language data as well as all translations
     */
    protected $translation = null;

    public function __construct()
    {
        $this->view = new ViewModel();
        $this->translation = new Container("zpc");
    }

    /**
     * Initialize any variables before controller actions
     *
     * @param \Zend\Mvc\MvcEvent $e
     * @method  IndexController::checkIdentity(bool $redirect, string $url)
     */
    public function onDispatch(MvcEvent $e)
    {
        parent::onDispatch($e);

        /**
         * @see IndexController::checkIdentity
         */
        if ($this->checkIdentity(false)) {
            $auth = new AuthenticationService();
            $this->view->identity = $auth->getIdentity();
        }
        $this->initTranslation();
        $this->initMenus();

        /**
         * Call this method only if we are not in Menu or News. Both of them calls the function by themselves
         */
        if ($this->params('controller') != "Application\Controller\Menu" && $this->params('controller') != "Application\Controller\News") {
            $this->initMetaTags();
        }
    }

/****************************************************
 * START OF ALL INIT FUNCTIONS
 ****************************************************/

    /**
     * Initialize translations.
     *
     * @return  void
     */
    private function initTranslation()
    {
        if (empty($this->translation->language)) {
            /**
             * Load English as default language.
             */
            // $terms = $this->getTable("term")->fetchJoin(false, "termtranslation", ["name"], ["translation"], "term.id=termtranslation.term", "inner", ["termtranslation.language" => $this->language()])->getDataSource();

            // foreach ($terms as $term) {
            //     $this->translation->$term['name'] = $term['translation'];
            // }
            $this->translation->language = 1;
        }
    }

    public function translatiossssssss()
    {
         $terms = $this->getTable("term")->fetchJoin(false, "termtranslation", ["name"], ["translation"], "term.id=termtranslation.term", "inner", ["termtranslation.language" => $this->language()])->getDataSource();
    }

    /**
     * Initialize menus and their submenus. 1 query to rule them all!
     *
     * First get all menus.
     * Second, itterate over each object and determinate if it's a submenu or not
     * Third separate each object based on it's type and prepare it for the view itteration
     *
     * @todo  make it dinamicly multilevel
     * @return void
     */
    private function initMenus()
    {
        $menu = $this->getTable("Menu")->fetchList(false, ["id", "caption", "menulink", "parent"], ["language" => $this->language()], "AND", null, "id, menuOrder");
        if (count($menu) > 0) {
            $menus = ["menus" => null, "submenus" => null];
            foreach ($menu as $submenu) {
                if ($submenu->getParent() > 0) {
                    /**
                     * This needs to have a second empty array in order to work
                     */
                    $menus["submenus"][$submenu->getParent()][] = $submenu;
                } else {
                    $menus["menus"][$submenu->getId()] = $submenu;
                }
            }
            $this->view->menus = $menus["menus"];
            $this->view->submenus = $menus["submenus"];
        }
        return $this->view;
    }

    /**
     * This function will generate all meta tags needed for SEO optimisation.
     *
     * @param  array $content
     * @return void
     */
    protected function initMetaTags(array $content = [])
    {
        $description = $keywords = $text = $preview = $title = $time = null;
        $plugin = $this->IndexPlugin();

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

        /**
         * @var Zend\View\Helper\Placeholder\Container $placeholder
         */
        $placeholder = $plugin->getCustomHead();
        $placeholder->append("\r\n<meta itemprop='name' content='Unnamed'>\r\n"); // must be sey from db
        $placeholder->append("<meta itemprop='description' content='".substr(strip_tags($text), 0, 150)."'>\r\n");
        $placeholder->append("<meta itemprop='title' content='".$title."'>\r\n");
        $placeholder->append("<meta itemprop='image' content='".$preview."'>\r\n");

        /**
         * @var Zend\View\Helper\HeadMeta $vhm
         */
        $vhm = $plugin->getHeadMeta();
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
        $vhm->appendProperty('og:locale', $this->language());
        // $vhm->appendProperty('article:published_time', date("Y-m-d H:i:s", time()));
        $vhm->appendProperty("og:title", $title);
        $vhm->appendProperty("og:description", $description);
        $vhm->appendProperty("og:type", 'article');
        $vhm->appendProperty("og:url", $this->getRequest()->getUri()->getHost().$this->getRequest()->getRequestUri());
        $vhm->appendHttpEquiv('cleartype', 'on');
        $vhm->appendHttpEquiv('x-dns-prefetch-control', 'on');

        /**
         * Other things that can be activated
         * Maybe allow full html tag input via a textarea. TODO: see if this is safe enough.
         */
        // <link href="https://plus.google.com/" rel="publisher" />
        // <meta name="google-site-verification" content="" />
    }

/****************************************************
 * START OF ALL MAIN/SHARED FUNCTIONS
 ****************************************************/

    /**
     * Get an instance of a database table of the desired model
     *
     * @param string $name
     * @return object
     */
    protected function getTable($name = null)
    {
        $plugin = $this->IndexPlugin();
        return $plugin->getTable($name);
    }

    /**
     * See if user is logged in.
     *
     * First we check to see if there is an identity stored.
     * If there is, we need to check for two parameters role and logged.
     * Those 2 parameters MUST always be of types int and bool.
     *
     * The redirect serves parameter is used to determinated
     * if we need to redirect the user to somewhere
     * else or just leave him access the desired area
     *
     * @param bool $redirect
     * @param string $url
     * @throws AuthorizationException
     * @return mixed
     */
    protected function checkIdentity($redirect = true, $url = "/")
    {
        $auth = new AuthenticationService();
        if ($auth->hasIdentity()) {
            if (!empty($auth->getIdentity()->role) &&
              ((int) $auth->getIdentity()->role === 1 || (int) $auth->getIdentity()->role === 10) &&
              isset($auth->getIdentity()->logged) && $auth->getIdentity()->logged === true) {
                /**
                 * If everything went fine, just return true and let the user access the desired area or make a redirect
                 */
                if ($redirect === false) {
                    return true;
                }
                return $this->redirect()->toUrl($url);
            }
            return $this->clearUserData($auth); // something is wrong, clear all user data
        }
        return null;
    }

    /**
     * Clear all session data and identities.
     * Throw an exception, which will be captured by the event manager and logged.
     *
     * @param AuthenticationService $auth
     * @throws AuthorizationException
     */
    private function clearUserData(AuthenticationService $auth = null)
    {
        $this->translation->getManager()->getStorage()->clear();
        $auth->clearIdentity();
        $this->translation = new Container("zpc");
        throw new AuthorizationException($this->translate("ERROR_AUTHORIZATION"));
    }

    /**
     * Shorthand method for getting params from URLs. Makes code easier to modify and avoids DRY code
     *
     * @param String $paramName
     * @param mixed $default
     * @return mixed
     */
    protected function getParam($paramName = null, $default = null)
    {
        $escaper = new \Zend\Escaper\Escaper('utf-8');
        $param = $this->params()->fromPost($paramName, 0);
        if (!$param) {
            $param = $this->params()->fromRoute($paramName, null);
        }
        if (!$param) {
            $param = $this->params()->fromQuery($paramName, null);
        }
        if (!$param) {
            $param = $this->params()->fromFiles($paramName, null);
        }
        /**
         * If this is array it MUST comes from fromFiles()
         */
        if (is_array($param) && !empty($param)) {
            return $param;
        }
        if (!$param) {
            return $default;
        }
        return $escaper->escapeHtml(trim($param));
    }

    /**
     * This method will iterate over an array and show its contents as separated strings
     * The method will accept an array with unlimited depth.
     *
     * <code>
     *     $myArray = [
     *         0 => 'A',
     *         1 => ['subA','subB',
     *                  [0 => 'subsubA', 1 => 'subsubB',
     *                      2 => [0 => 'subsubsubA', 1 => 'subsubsubB']
     *                  ]
     *              ],
     *         2 => 'B',
     *         3 => ['subA','subB','subC'],
     *         4 => 'C'
     *     ];
     *     $this->setLayoutMessages($myArray, "default");
     * </code>
     *
     * @param array|arrayobject|string $message
     * @param string $namespace determinates the message layout and color. It's also used for the flashMessenger namespace
     * @return ViewModel
     */
    protected function setLayoutMessages($message = [], $namespace = 'default')
    {
        $flashMessenger = $this->flashMessenger();

        if (!in_array($namespace, ["success", "error", "warning", 'info', 'default'])) {
            $namespace = 'default';
        }

        $flashMessenger->setNamespace($namespace);

        $iterator = new \RecursiveArrayIterator((array) $message);

        while ($iterator->valid()) {
            if ($iterator->hasChildren()) {
                $this->setLayoutMessages($iterator->getChildren(), $namespace);
            } else {
                $flashMessenger->addMessage($iterator->current(), $namespace);
            }
            $iterator->next();
        }

        $this->view->flashMessages = $flashMessenger;
        return $this->view;
    }

    /**
     * @param  int $code error code
     * @return  ViewModel
     */
    protected function setErrorCode($code = 404)
    {
        $this->getResponse()->setStatusCode($code);
        $this->view->setVariables([
            'message' => '404 Not found',
            'reason' => 'The link you have requested doesn\'t exists',
            'exception' => "",
        ]);
        $this->view->setTemplate('error/index');
        return $this->view;
    }

    /**
     * Show translated message. ERROR will be used as a default constant.
     *
     * If the given offset doesn't exist
     * the object will automatically return empty string
     *
     * @param string $str the constant name from the database. It should always be upper case.
     * @return string
     */
    public function translate($str = "ERROR")
    {
        if (!empty($this->translation)) {
            return (string) $this->translation->offsetGet($str);
        }
        return "";
    }

    /**
     * Get Language id
     * @return int
     */
    protected function language()
    {
        if (isset($this->translation->language) && (int) $this->translation->language > 0) {
            return $this->translation->language;
        }
        $this->translation->language = 1;
        return $this->translation->language;
    }

/****************************************************
 * START OF ALL ACTION METHODS
 ****************************************************/

    /**
     * Main websites view
     *
     * @return ViewModel
     */
    public function indexAction()
    {
        $this->view->setTemplate("application/index/index");
        return $this->view;
    }

    /**
     * Select new language
     *
     * This will reload the translations every time the method is being called
     */
    public function languageAction()
    {
        $terms = $this->getTable("term")->fetchJoin(false, "termtranslation", ["name"], ["translation"], "term.id=termtranslation.term", "inner", ["termtranslation.language" => (int) $this->getParam("id")]);

        if ($terms) {
            foreach ($terms->getDataSource() as $term) {
                $this->translation->offsetSet($term['name'], $term['translation']);
            }
            $this->translation->language = (int) $this->getParam("id");
        }
        return $this->redirect()->toUrl("/");
    }
}
