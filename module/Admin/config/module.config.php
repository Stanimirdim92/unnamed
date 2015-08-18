<?php
return [
    'router' => [
        'routes' => [
            'admin' => [
                'type'    => 'Literal',
                'options' => [
                    'route' => '/admin',
                    'defaults' => [
                        '__NAMESPACE__' => 'Admin\Controller',
                        'controller'    => 'Index',
                        'action'        => 'index',
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'default' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/[:controller[/][:action[/][:id][/page/:page][/search/:search]]]',
                            'constraints' => [
                                'controller' => '[a-zA-Z0-9_-]*',
                                'action'     => '[a-zA-Z0-9_-]*',
                                'search'     => '[a-zA-Z0-9_-]*',
                                'id'         => '[0-9]+',
                                'page'       => '[0-9]+',
                            ],
                            'defaults' => [
                                '__NAMESPACE__' => 'Admin\Controller',
                                'controller'    => 'Index',
                                'action'        => 'index',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
    'service_manager' => [
        'factories' => [
            'AdminErrorHandling'          => 'Admin\Factory\AdminErrorHandlingFactory',
            'AdministratorTable'          => 'Admin\Factory\Model\AdministratorTableFactory',
            'ContentTable'                => 'Admin\Factory\Model\ContentTableFactory',
            'LanguageTable'               => 'Admin\Factory\Model\LanguageTableFactory',
            'MenuTable'                   => 'Admin\Factory\Model\MenuTableFactory',
            'UserTable'                   => 'Admin\Factory\Model\UserTableFactory',
            'AdminMenuTable'              => 'Admin\Factory\Model\AdminMenuTableFactory',
        ],
    ],
    'controllers' => [
        'factories' => [
            'Admin\Controller\Content'       => 'Admin\Factory\Controller\ContentControllerFactory',
            'Admin\Controller\Menu'          => 'Admin\Factory\Controller\MenuControllerFactory',
            'Admin\Controller\Language'      => 'Admin\Factory\Controller\LanguageControllerFactory',
            'Admin\Controller\Administrator' => 'Admin\Factory\Controller\AdministratorControllerFactory',
            'Admin\Controller\AdminMenu'     => 'Admin\Factory\Controller\AdminMenuControllerFactory',
            'Admin\Controller\User'          => 'Admin\Factory\Controller\UserControllerFactory',
        ],
        'invokables' => [
            'Admin\Controller\Index' => 'Admin\Controller\IndexController',
        ],
    ],
    'form_elements' => [
        'factories' => [
            'Admin\Form\ContentForm'      => 'Admin\Factory\Form\ContentFormFactory',
            'Admin\Form\MenuForm'         => 'Admin\Factory\Form\MenuFormFactory',
            'Admin\Form\AdminMenuForm'    => 'Admin\Factory\Form\AdminMenuFormFactory',
        ],
    ],
    'view_manager' => [
        'display_not_found_reason' => (APP_ENV === "development"),
        'display_exceptions'       => (APP_ENV === "development"),
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/index',
        'exception_template'       => 'error/index',
        // This can be used as the default suffix for template scripts resolving, it defaults to 'phtml'.
        // 'default_template_suffix' => 'phtml',
        'template_map' => include __DIR__ . '/../template_map.php',
        'strategies' => [
            'ViewJsonStrategy',
        ],
    ],
];
