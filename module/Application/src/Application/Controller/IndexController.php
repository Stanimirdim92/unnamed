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
 * @category   Application\Index
 * @package    ZendPress
 * @author     Stanimir Dimitrov <stanimirdim92@gmail.com>
 * @copyright  2015 Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 * @version    0.03
 * @link       TBA
 */
namespace Application\Controller;

use Zend\Session\Container;
use Zend\Authentication\AuthenticationService;
use Custom\Plugins\Functions;
use Custom\Plugins\Mailing;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\Form\ContactForm;

class IndexController extends AbstractActionController
{
    /**
     * @var null $cache holds any other session information, contains warning, success and error vars that are shown just once and then reset
     * @return Zend\Session\Container
     */
    protected $cache = null;

    /**
     * @var null $view creates instance to view model
     * @return Zend\View\Model\ViewModel
     */
    protected $view = null;

    /**
     * @var null $translation holds language data as well as all translations
     * @return Zend\Session\Container
     */
    protected $translation = null;

    /**
     * DRY variable to hold the language. Easier to work with
     *
     * @var null
     * @return int from $this->translation->language
     */
    protected $langTranslation = null;

    /**
     * @var ContactForm
     */
    protected $contactForm = null;

    /**
     * @var Zend\View\Helper\Placeholder\Container $customHead
     */
    protected $customHead = null;

    /**
     * @var Zend\View\Helper\HeadMeta $headMeta
     */
    protected $headMeta = null;

    /**
     * Used to detect actions without IDs. Inherited in all other classes
     */
    const NO_ID = 'ID not found';

    /**
     * @param Application\Form\ContactForm $contactForm
     * @param Zend\View\Helper\Placeholder\Container $customHead
     * @param Zend\View\Helper\HeadMeta $headMeta
     */
    public function __construct(ContactForm $contactForm = null, $customHead = null, $headMeta = null)
    {
        $this->view = new ViewModel();
        $this->contactForm = $contactForm;
        $this->customHead = $customHead;
        $this->headMeta = $headMeta;

        $this->initCache();
        $this->initTranslation();
    }

    /**
     * Initialize any variables before controller actions
     *
     * @param \Zend\Mvc\MvcEvent $e
     * @method  IndexController::checkIdentity(bool $redirect, string $url)
     */
    public function onDispatch(\Zend\Mvc\MvcEvent $e)
    {
        parent::onDispatch($e);

        /**
         * @see IndexController::checkIdentity
         */
        if ($this->checkIdentity(false)) {
            $auth = new AuthenticationService();
            $this->view->identity = $auth->getIdentity();
        }
        $this->initMenus();
        $this->initViewVars();
        // not working properly die to the passed classes into the consturctor. they are not being shared...
        // $this->initMetaTags();
        return $this->view;
    }

/****************************************************
 * START OF ALL INIT FUNCTIONS
 ****************************************************/

    /**
     * initialize any session variables in this method
     * @return void
     */
    private function initCache()
    {
        $this->cache = new Container("cache");
        return $this->view;
    }

    /**
     * initialize any view related stuff
     * @return void
     */
    private function initViewVars()
    {
        if (!isset($this->translation->languageObject)) {
            $this->translation->languageObject = $this->getTable("Language")->getLanguage($this->langTranslation);
            $this->view->langName = $this->translation->languageObject->getName();
        }

        $this->view->cache = $this->cache;
        $this->view->translation = $this->translation;
        $this->view->baseURL = $this->getRequest()->getUri()->getHost().$this->getRequest()->getRequestUri();
        $this->view->languages = $this->getTable("Language")->fetchList(false, [], ["active" => 1], "AND", null, "name ASC");
        return $this->view;
    }

    /**
     * initialize languages and language-related stuff like translations.
     * @return  void
     */
    private function initTranslation()
    {
        $this->translation = new Container("translations");

        /**
         * Load English as default language.
         * Maybe make this possible to change via backend?
         */
        if (!isset($this->translation->language)) {
            $this->translation = Functions::initTranslations(1, true);
            $this->translation->language = 1;
        }

        // keeping it simple and DRY
        $this->langTranslation = ((int) $this->translation->language > 0 ? $this->translation->language : 1);
        return $this->view;
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
        $menu = $this->getTable("Menu")->fetchList(false, ["id", "caption", "menulink", "parent",], ["language" => $this->langTranslation], "AND", null, "id, menuOrder");
        if (count($menu) > 0) {
            $menus = [];
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
     * @param  Pdo\Result|Content $content
     * @return void
     */
    protected function initMetaTags($content = null)
    {
        $description = $keywords = $text = $preview = $title = null;
        if (!empty($content)) {
            /**
             * If there is a menu attached to this content, get its SEO metadata
             */
            if ($content->current()->getMenu() > 0) {
                $isMenuObject = $content->current()->getMenuObject();
                $description = $isMenuObject->getDescription();
                $keywords = $isMenuObject->getKeywords();
            }

            $text = $content->current()->getText();
            $preview = $content->current()->getPreview();
            $title = $content->current()->getTitle();
        }

        // must be set from db
        (empty($description) ? $description = "lorem ipsum dolar sit amet" : $description);
        (empty($text) ? $text = "lorem ipsum dolar sit amet" : $text);
        (empty($keywords) ? $keywords = "lorem, ipsum, dolar, sit, amet" : $keywords);
        (empty($preview) ? $preview = "" : $preview);
        (empty($title) ? $title = "ZendPress" : $title);

        $placeholder = $this->customHead;
        $placeholder->append("<meta itemprop='name' content='ZendPress'>\r\n"); // must be sey from db
        $placeholder->append("<meta itemprop='description' content='".substr(strip_tags($text), 0, 150)."..."."'>\r\n");
        $placeholder->append("<meta itemprop='title' content='".$title."'>\r\n");
        $placeholder->append("<meta itemprop='image' content='".$preview."'>\r\n");
        $vhm = $this->headMeta;
        $vhm->appendName('keywords', $keywords);
        $vhm->appendName('description', $description);
        $vhm->appendProperty('og:image', $preview);
        $vhm->appendProperty("og:title", $title);
        $vhm->appendProperty("og:description", $description);
    }

/****************************************************
 * START OF ALL MAIN/SHARED FUNCTIONS
 ****************************************************/

    /**
     * @param null $name
     * @return ObjectTable
     */
    protected function getTable($name = null)
    {
        if (!is_string($name) || empty($name)) {
            throw new \InvalidArgumentException(__METHOD__ . ' must be string and must not be empty');
        }
        return $this->getServiceLocator()->get($name . "Table");
    }

    /**
     * See if user is logged in.
     * @param bool $redirect
     * @param string $url
     * @throws AuthorizationException
     * @return mixed
     */
    protected function checkIdentity($redirect = true, $url = "/")
    {
        $auth = new AuthenticationService();
        if ($auth->hasIdentity()) {
            if (isset($auth->getIdentity()->role) &&
              ((int) $auth->getIdentity()->role === 1 || (int) $auth->getIdentity()->role === 10) &&
              isset($auth->getIdentity()->logged) && (bool) $auth->getIdentity()->logged === true) {
                /**
                 * If everything went fine, just return true and let the user access the desired area or make a redirect
                 */
                if ((bool) $redirect === false) {
                    return true;
                }
                return $this->redirect()->toUrl($url);
            }
            return $this->clearUserData($auth); // something is wrong, clear all user data
        }
    }

    /**
     * @param AuthenticationService $auth
     */
    private function clearUserData(AuthenticationService $auth = null)
    {
        $this->cache->getManager()->getStorage()->clear();
        $this->translation->getManager()->getStorage()->clear();
        $auth->clearIdentity();
        $this->cache = null;
        $this->translation = null;
        throw new \Custom\Error\AuthorizationException($this->translate("ERROR_AUTHORIZATION"));
    }

    /**
     * Shorthand method for getting params from URLs. Makes code easier to modify and avoids DRY code
     *
     * @param String $paramName
     * @param null $default
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
        if (!$param) {
            return $default;
        }
        return $escaper->escapeHtml(trim($param));
    }

    /**
     *
     * @param null|string $message
     * @param null|string $namespace
     */
    protected function setLayoutMessages($message = null, $namespace = 'default')
    {
        $flashMessenger = $this->flashMessenger();
        $messages = [];
        $arrayMessages = [];

        if (!in_array($namespace, ["success", "error", "warning", 'info', 'default'])) {
            $namespace = 'default';
        }

        $flashMessenger->setNamespace($namespace);
        if (is_array($message)) {
            foreach ($message as $msg) {
                if (is_array($msg)) {
                    foreach ($msg as $text) {
                        $flashMessenger->addMessage($text, $namespace);
                    }
                } else {
                    $flashMessenger->addMessage($msg, $namespace);
                }
            }
        } else {
            $flashMessenger->addMessage($message, $namespace);
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
        $this->view->setTemplate('error/index.phtml');
        return $this->view;
    }

    /**
     * Show translated message
     */
    public function translate($str = null)
    {
        return (string) $this->translation->{$str};
    }

/****************************************************
 * START OF ALL ACTION METHODS
 ****************************************************/

    /**
     * Init everything
     */
    public function indexAction()
    {
        return $this->view;
    }


    /**
     * Select new language and reload all text
     */
    public function languageAction()
    {
        $this->translation->languageObject = $this->getTable("Language")->getLanguage($this->getParam("id"));
        $this->view->langName = $this->translation->languageObject->getName();

        /**
         * This will reload the translations every time the method is being called
         */
        $this->translation = Functions::initTranslations($this->translation->languageObject->getId(), true);
        $this->langTranslation = $this->translation->language = $this->translation->languageObject->getId();

        return $this->redirect()->toUrl("/");
    }

    /**
     * Simple contact form
     */
    public function contactAction()
    {
        $form = $this->contactForm;
        $form->get("email")->setLabel($this->translate("EMAIL"));
        $form->get("name")->setLabel($this->translate("NAME"))->setAttribute("placeholder", $this->translate("NAME"));
        $form->get("subject")->setLabel($this->translate("SUBJECT"))->setAttribute("placeholder", $this->translate("SUBJECT"));
        $form->get("captcha")->setLabel($this->translate("CAPTCHA"))->setAttribute("placeholder", $this->translate("ENTER_CAPTCHA"));
        $form->get("message")->setLabel($this->translate("MESSAGE"))->setAttribute("placeholder", $this->translate("ENTER_MESSAGE"));

        $this->view->form = $form;
        if ($this->getRequest()->isPost()) {
            $form->setInputFilter($form->getInputFilter());
            $form->setData($this->getRequest()->getPost());
            if ($form->isValid()) {
                $formData = $form->getData();
                $to = "psyxopat@gmail.com"; // must be set from db
                try {
                    $result = Mailing::sendMail($to, '', $formData['subject'], $formData['message'], $formData['email'], $formData['name']);
                    $this->setLayoutMessages($this->translat("CONTACT_SUCCESS"), 'success');
                } catch (\Exception $e) {
                    $this->setLayoutMessages($this->translat("CONTACT_ERROR"), 'error');
                }
            } else {
                $this->setLayoutMessages($form->getMessages(), 'error');
            }
        }
        return $this->view;
    }
}
