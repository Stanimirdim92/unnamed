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
 * @category   Admin\Module
 * @package    ZendPress
 * @author     Stanimir Dimitrov <stanimirdim92@gmail.com>
 * @copyright  2015 Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 * @version    0.03
 * @link       TBA
 */

namespace Admin;

use Admin\Model\Term;
use Admin\Model\TermTable;
use Admin\Model\TermCategory;
use Admin\Model\TermCategoryTable;
use Admin\Model\TermTranslation;
use Admin\Model\TermTranslationTable;
use Admin\Model\Language;
use Admin\Model\LanguageTable;
use Admin\Model\User;
use Admin\Model\UserTable;
use Admin\Model\AdminMenu;
use Admin\Model\AdminMenuTable;
use Admin\Model\Administrator;
use Admin\Model\AdministratorTable;

use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;

use Zend\ServiceManager\ServiceLocatorInterface;

use Zend\ModuleManager\Feature;
use Zend\Mvc\MvcEvent;

class Module implements Feature\AutoloaderProviderInterface,
                        Feature\ServiceProviderInterface,
                        Feature\ConfigProviderInterface
{
    /**
     * make sure to log errors and redirect to admin index
     * @param \Zend\Mvc\MvcEvent $e
     */
    public function onBootstrap(MvcEvent $e)
    {
        $app = $e->getTarget();
        $em = $app->getEventManager();
        $sm = $app->getServiceManager();

        $em->attach(MvcEvent::EVENT_DISPATCH_ERROR, function (MvcEvent $e) use ($sm)
        {
            if (!$e->getParam("exception"))
            {
                return $this->errorResponse($e);
            }
            else
            {
                return $this->logError($sm->get('AdminErrorHandling'), $e->getParam("exception"), $e, $sm, "Guest");
            }
        });
    }

    /**
     * @param  AdminErrorHandling $service
     * @param  Exception $exception
     * @param  MvcEvent $e
     * @param  ServiceManager $sm
     *
     * @return Error
     */
    private function logError($service, $exception, $e, $sm, $userRole = null)
    {
        if($exception instanceof \Custom\Error\AuthorizationException)
        {
            $cache = new \Zend\Session\Container("cache");
            $remote = new \Zend\Http\PhpEnvironment\RemoteAddress();
            if ($cache->role == 1)
            {
                $userRole = $cache->role;
            }
            else if ($cache->role == 10)
            {
                $userRole = $cache->role;
            }
            $message = " *** ADMIN LOG ***
            Controller: " . $e->getRouteMatch()->getParam('controller') . ",
            Controller action: " . $e->getRouteMatch()->getParam('action') . ",
            User role: " . $userRole. ",
            User id: " . (isset($cache->user->id) ? $cache->user->id : "Guest"). ",
            Admin: " . (isset($cache->user->admin) ? "Yes" : "No"). ",
            IP: " . $remote->getIpAddress() . ",
            Browser string: " . $sm->get("Request")->getServer()->get('HTTP_USER_AGENT') . ",
            Date: " . date("Y-m-d H:i:s", time()) . ",
            Full URL: ".$sm->get("Request")->getRequestUri().",
            User port: ".$_SERVER["REMOTE_PORT"].",
            Remote host addr: ".gethostbyaddr($remote->getIpAddress()).",
            Method used: " . $sm->get("Request")->getMethod() . "\n";
            $service->logAuthorisationError($message);
        }
        else
        {
            $service->logException($exception);
        }
        $this->errorResponse($e);
    }

    /**
     * @param \Zend\Mvc\MvcEvent $e
     */
    private function errorResponse(MvcEvent $e)
    {
        $e->getResponse()->setStatusCode(404);
        $e->getViewModel()->setTemplate('layout/error-layout');
        $e->stopPropagation();
    }

    public function getConfig()
    {
        $config = include __DIR__ . '/config/module.config.php';
        $config['controllers'] = array(
            'invokables' => array(
                'Admin\Controller\Index'            => 'Admin\Controller\IndexController',
                'Admin\Controller\AdminMenu'        => 'Admin\Controller\AdminMenuController',
                'Admin\Controller\Term'             => 'Admin\Controller\TermController',
                'Admin\Controller\TermCategory'     => 'Admin\Controller\TermCategoryController',
                'Admin\Controller\TermTranslation'  => 'Admin\Controller\TermTranslationController',
                'Admin\Controller\User'             => 'Admin\Controller\UserController',
                'Admin\Controller\Administrator'    => 'Admin\Controller\AdministratorController',
                'Admin\Controller\Language'         => 'Admin\Controller\LanguageController',
                'Admin\Controller\Menu'             => 'Admin\Controller\MenuController',
                'Admin\Controller\Content'          => 'Admin\Controller\ContentController',
            ),
        );
        return $config;
    }

    public function getViewHelperConfig()
    {
        return array(
            'factories' => array(
                'Params' => function (ServiceLocatorInterface $helpers)
                {
                    $app = $helpers->getServiceLocator()->get('Application');
                    return new \Admin\View\Helper\Params($app->getRequest(), $app->getMvcEvent());
                }
            ),
        );
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__
                ),
            ),
        );
    }

    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'AdminErrorHandling' =>  'Admin\Factory\AdminErrorHandlingFactory',

                'AdminMenuTable' => function ($sm)
                {
                    return new AdminMenuTable($sm);
                },
                'AdminMenuTableGateway' => function ($sm)
                {
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new AdminMenu(null, $sm));
                    return new TableGateway('adminmenu', $sm->get('Zend\Db\Adapter\Adapter'), null, $resultSetPrototype);
                },

                'UserTable' => function ($sm)
                {
                    return new UserTable($sm);
                },
                'UserTableGateway' => function ($sm)
                {
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new User(null, $sm));
                    return new TableGateway('user', $sm->get('Zend\Db\Adapter\Adapter'), null, $resultSetPrototype);
                },


                'AdministratorTable' => 'Admin\Factory\AdministratorTableFactory',
                'ContentTable' => 'Admin\Factory\ContentTableFactory',
                'LanguageTable' => 'Admin\Factory\LanguageTableFactory',
                'MenuTable'    => 'Admin\Factory\MenuTableFactory',
            ),
        );
    }
}
