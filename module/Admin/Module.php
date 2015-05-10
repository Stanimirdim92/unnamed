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
use Admin\Model\User;
use Admin\Model\UserTable;
use Admin\Model\AdminMenu;
use Admin\Model\AdminMenuTable;

use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;

use Zend\ModuleManager\Feature;
use Zend\Session\Container;
use Zend\Mvc\MvcEvent;
use Zend\EventManager\EventInterface;

class Module implements
    Feature\AutoloaderProviderInterface,
    Feature\ConfigProviderInterface,
    Feature\BootstrapListenerInterface
{
    /**
     * @param EventInterface $e
     */
    public function onBootstrap(EventInterface $e)
    {
        $em = $e->getApplication()->getEventManager();
        $sm = $e->getApplication()->getServiceManager();

        $em->attach(MvcEvent::EVENT_DISPATCH_ERROR, function (MvcEvent $e) use ($sm) {
            return $this->logError($sm->get('AdminErrorHandling'), $e, $sm);
        });
    }

    /**
     * @param  AdminErrorHandling $service
     * @param  Zend\Mvc\MvcEvent $e
     * @param  ServiceManager $sm
     * @param  $userRole int
     *
     * @return void
     */
    private function logError($service, $e, $sm, $userRole = "Guest")
    {
        if ($e->getParam("exception") instanceof \Custom\Error\AuthorizationException) {
            $service->logAuthorisationError($e, $sm, new Container("cache"), $userRole);
        } elseif ($e->getParam("exception") != null) {
            $service->logException($e->getParam("exception"));
        } else {
            return $this->errorResponse($e);
        }
        return $this->errorResponse($e);
    }

    /**
     * This function is used to simulate a fake redirect to errors page,
     * where it will show a friendly error message to the user.
     * The error message comes from the throwed exception.
     * Also make sure that we always send a 404 response.
     *
     * @param \Zend\Mvc\MvcEvent $e
     * @return  MvcEvent
     */
    private function errorResponse(MvcEvent $e)
    {
        $e->getResponse()->setStatusCode(404);
        $e->getViewModel()->setVariables([
            'message' => '404 Not found',
            'reason' => 'Error',
            'exception' => ($e->getParam("exception") ? $e->getParam("exception")->getMessage(): ""),
        ]);
        $e->getViewModel()->setTemplate('error/index.phtml');
        $e->stopPropagation();
        return $e;
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return [
            'Zend\Loader\ClassMapAutoloader' => [
                __DIR__ . '/autoload_classmap.php',
            ],
            'Zend\Loader\StandardAutoloader' => [
                'namespaces' => [
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ],
            ],
        ];
    }

    public function getServiceConfig()
    {
        return [
            'factories' => [
                'AdminMenuTable' => function ($sm) {
                    return new AdminMenuTable($sm);
                },
                'AdminMenuTableGateway' => function ($sm) {
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new AdminMenu(null, $sm));
                    return new TableGateway('adminmenu', $sm->get('Zend\Db\Adapter\Adapter'), null, $resultSetPrototype);
                },
                'TermTable' => function ($sm) {
                    $table = new TermTable($sm);
                    return $table;
                },
                'TermTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Term());
                    return new TableGateway('term', $dbAdapter, null, $resultSetPrototype);
                },

                'TermCategoryTable' => function ($sm) {
                    $table = new TermCategoryTable($sm);
                    return $table;
                },
                'TermCategoryTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new TermCategory());
                    return new TableGateway('termcategory', $dbAdapter, null, $resultSetPrototype);
                },

                'TermTranslationTable' => function ($sm) {
                    $table = new TermTranslationTable($sm);
                    return $table;
                },
                'TermTranslationTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new TermTranslation());
                    return new TableGateway('termtranslation', $dbAdapter, null, $resultSetPrototype);
                },
                'UserTable' => function ($sm) {
                    return new UserTable($sm);
                },
                'UserTableGateway' => function ($sm) {
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new User(null, $sm));
                    return new TableGateway('user', $sm->get('Zend\Db\Adapter\Adapter'), null, $resultSetPrototype);
                },
                'AdminErrorHandling' =>  'Admin\Factory\AdminErrorHandlingFactory',
                'AdministratorTable' => 'Admin\Factory\AdministratorTableFactory',
                'ContentTable' => 'Admin\Factory\ContentTableFactory',
                'LanguageTable' => 'Admin\Factory\LanguageTableFactory',
                'MenuTable'    => 'Admin\Factory\MenuTableFactory',
            ],
        ];
    }
}
