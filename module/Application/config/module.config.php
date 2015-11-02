<?php

/**
 * @copyright  2015 (c] Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 *
 * @version    0.0.21
 *
 * @link       TBA
 */

namespace Application;

return [
    'router' => [
        'routes' => [
            'application' => [
                'type'    => 'Zend\Mvc\Router\Http\Literal',
                'options' => [
                    'route'    => '/',
                    'defaults' => [
                        'controller'  => 'Application\Controller\Index',
                        'action'      => 'index',
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'default' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '[:controller[/][:action[/[:id][token/:token][:post]][/page/:page][/search/:search]]]',
                            'constraints' => [
                                'controller' => '[a-zA-Z0-9_-]*',
                                'action'     => '[a-zA-Z0-9_-]*',
                                'token'      => '[a-zA-Z-+_/&0-9]*',
                                'post'       => '[a-zA-Z0-9_-]*',
                                'search'     => '[a-zA-Z0-9_-]*',
                                'id'         => '[0-9]+',
                                'page'       => '[0-9]+',
                            ],
                            'defaults' => [
                                '__NAMESPACE__' => 'Application\Controller',
                                'controller'    => 'Index',
                                'action'        => 'index',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
    'controllers' => [
        'factories' => [
            'Application\Controller\Login'        => Factory\Controller\LoginControllerFactory::class,
            'Application\Controller\Contact'      => Factory\Controller\ContactControllerFactory::class,
            'Application\Controller\Registration' => Factory\Controller\RegistrationControllerFactory::class,
        ],
        'invokables' => [
            'Application\Controller\Base'  => Controller\BaseController::class,
            'Application\Controller\Index' => Controller\IndexController::class,
            'Application\Controller\News'  => Controller\NewsController::class,
            'Application\Controller\Menu'  => Controller\MenuController::class,
        ],
    ],
    'doctrine' => [
        'driver' => [
            __NAMESPACE__ . '_driver' => [
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => [
                    __DIR__ . '/../src/' . __NAMESPACE__ . '/Model',
                ],
            ],
            'orm_default' => [
                'drivers' => [
                    __NAMESPACE__ . '\Model' => __NAMESPACE__ . '_driver',
                ],
            ],
        ],
    ],
];
