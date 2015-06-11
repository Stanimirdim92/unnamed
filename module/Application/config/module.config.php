<?php
return [
    'router' => [
        'routes' => [
            'home' => [
                'type' => 'Literal',
                'options' => [
                    'route'    => '[/]',
                    'defaults' => [
                        'controller' => 'Application\Controller\Index',
                        'action'     => 'index',
                    ],
                ],
            ],
            'application' => [
                'type'    => 'Segment',
                'options' => [
                    'route' => '/[:controller[/][:action[[/id/:id][/token/:token]][/page/:page][/search/:search]]]',
                    'constraints' => [
                        'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'         => '[0-9]+',
                        'token'      => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'page'       => '[0-9]+',
                        'search'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ],
                    'defaults' => [
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller'    => 'Index',
                        'action'        => 'index',
                    ],
                ],
            ],
            'contact'     => [
                'type'    => 'Literal',
                'options' => [
                    'route'    => '/contact',
                    'defaults' => [
                        'controller' => 'Application\Controller\Index',
                        'action'     => 'contact',
                    ],
                ],
            ],
            'news' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/news[/][post/:post][/page/:page]',
                    'constraints' => [
                        'post' => '[a-zA-Z][a-zA-Z0-9_-]+',
                        'page' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => 'Application\Controller\News',
                        'action'     => 'news',
                    ],
                ],
            ],
            'menu' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/menu[/][:title]',
                    'constraints' => [
                        'title' => '[a-zA-Z][a-zA-Z0-9_-]+',
                    ],
                    'defaults' => [
                        'controller' => 'Application\Controller\Menu',
                        'action'     => 'menu',
                    ],
                ],
            ],
            'admin' => [
                'type'    => 'Segment',
                'options' => [
                    'route' => '/admin[/][:controller[/][:action[/id/:id][/page/:page][/search/:search]]]',
                    'constraints' => [
                        'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'         => '[0-9]+',
                        'page'       => '[0-9]+',
                        'search'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ],
                    'defaults' => [
                        '__NAMESPACE__' => 'Admin\Controller',
                        'controller' => 'Index',
                        'action'     => 'index',
                    ],
                ],
            ],
        ],
    ],
    'service_manager' => [
        'abstract_factories' => [
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory',
        ],
        'factories' => [
            'ApplicationErrorHandling' => 'Application\Factory\ApplicationErrorHandlingFactory',
            'Params'                   => 'Application\Factory\ParamsFactory',
            'ResetPasswordTable'       => "Application\Factory\ResetPasswordTableFactory",
        ],
    ],
    'controllers' => [
        'invokables' => [
            'Application\Controller\News'         => 'Application\Controller\NewsController',
            'Application\Controller\Menu'         => 'Application\Controller\MenuController',
        ],
        'factories' => [
            'Application\Controller\Registration' => "Application\Factory\Controller\RegistrationFormFactory",
            'Application\Controller\Index'        => "Application\Factory\Controller\IndexControllerFactory",
            'Application\Controller\Login'        => "Application\Factory\Controller\LoginFormFactory",
        ],
    ],
    'view_helpers' => [
        'factories' => [
        ],
    ],
    'view_manager' => [
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/layout',
        'exception_template'       => 'error/index',
        // This can be used as the default suffix for template scripts resolving, it defaults to 'phtml'.
        // 'default_template_suffix' => 'php',
        'template_map' => [
            'application/layout'      => __DIR__ . '/../../Application/view/layout/layout.phtml',
            'error/layout'            => __DIR__ . '/../../Application/view/error/layout.phtml',
            'error/index'             => __DIR__ . '/../../Application/view/error/index.phtml',
            'error/404'               => __DIR__ . '/../../Application/view/error/index.phtml',
        ],
        'template_path_stack' => [
            'Application'     => __DIR__ . '/../../Application/view',
            'Admin'           => __DIR__ . '/../../Admin/view',
        ],
        'strategies' => [
            'ViewJsonStrategy',
        ],
    ],
    'module_layouts' => [
        'Application' => 'layout/layout',
        'Admin'       => 'layout/admin',
    ],
];
