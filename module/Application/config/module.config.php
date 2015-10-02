<?php

/**
 * @copyright  2015 (c) Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 *
 * @version    0.0.15
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
                            'route'    => '[:controller[/][:action[/[:id][token/:token][:title]][search/:search]]]',
                            'constraints' => [
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'token'      => '[a-zA-Z][a-zA-Z-+_/&0-9]*',
                                'title'      => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'search'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id'         => '[a-zA-Z][0-9]+',
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
            'news' => [
                'type'    => 'Zend\Mvc\Router\Http\Literal',
                'options' => [
                    'route'    => '/news',
                    'constraints' => [
                        'post' => '[a-zA-Z0-9_-]*',
                        'page' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => 'Application\Controller\News',
                        'action'     => 'post',
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'default' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/[post/:post][page/:page]',
                            'constraints' => [
                                'post' => '[a-zA-Z0-9_-]*',
                                'page' => '[0-9]+',
                            ],
                            'defaults' => [
                                '__NAMESPACE__' => 'Application\Controller',
                                'controller'    => 'News',
                                'action'        => 'post',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
    'service_manager' => [
        'factories' => [
            'ApplicationErrorHandling' => Factory\ApplicationErrorHandlingFactory::class,
            'ResetPasswordTable'       => Factory\Model\ResetPasswordTableFactory::class,
            'initSession'              => Factory\ApplicationSessionFactory::class,
        ],
    ],
    'controllers' => [
        'factories' => [
            'Application\Controller\Login'        => Factory\Controller\LoginControllerFactory::class,
            'Application\Controller\Contact'      => Factory\Controller\ContactControllerFactory::class,
            'Application\Controller\Registration' => Factory\Controller\RegistrationControllerFactory::class,
        ],
        'invokables' => [
            'Application\Controller\Index' => Controller\IndexController::class,
            'Application\Controller\News'  => Controller\NewsController::class,
            'Application\Controller\Menu'  => Controller\MenuController::class,
        ],
    ],
    'view_manager' => [
        'template_map' => include __DIR__ . '/../template_map.php',
    ],
];
