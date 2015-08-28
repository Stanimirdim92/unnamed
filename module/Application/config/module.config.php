<?php
return [
    'router' => [
        'routes' => [
            'application' => [
                'type'    => 'Literal',
                'options' => [
                    'route'    => '/',
                    'defaults' => [
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller'    => 'Index',
                        'action'        => 'index',
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'default' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '[:controller[/][:action[/[:id][token/:token][:title]][search/:search]]]',
                            'constraints' => [
                                'controller' => '[a-zA-Z0-9_-]*',
                                'action'     => '[a-zA-Z0-9_-]*',
                                'token'      => '[a-zA-Z-+_/&0-9]*',
                                'title'      => '[a-zA-Z0-9_-]*',
                                'search'     => '[a-zA-Z0-9_-]*',
                                'id'         => '[0-9]+',
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
                'type' => 'Literal',
                'options' => [
                    'route'    => '/news',
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
            'ApplicationErrorHandling' => 'Application\Factory\ApplicationErrorHandlingFactory',
            'ResetPasswordTable'       => 'Application\Factory\Model\ResetPasswordTableFactory',
            'initSession'              => 'Application\Factory\ApplicationSessionFactory',
        ],
    ],
    'controllers' => [
        'factories' => [
            'Application\Controller\Login'        => 'Application\Factory\Controller\LoginControllerFactory',
            'Application\Controller\Contact'      => 'Application\Factory\Controller\ContactControllerFactory',
            'Application\Controller\Registration' => 'Application\Factory\Controller\RegistrationControllerFactory',
        ],
        'invokables' => [
            'Application\Controller\Index'        => 'Application\Controller\IndexController',
            'Application\Controller\News'         => 'Application\Controller\NewsController',
            'Application\Controller\Menu'         => 'Application\Controller\MenuController',
        ],
    ],
    'view_manager' => [
        'template_map' => include __DIR__ . '/../template_map.php',
    ],
];
