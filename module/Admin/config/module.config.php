<?php
return [
    'router' => [
        'routes' => [
            'admin' => [
                'type'    => 'Segment',
                'options' => [
                    'route' => '/admin[/][:controller[/][:action[/:id][/page/:page][/search/:search]]]',
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
    'service_manager' => [
        'factories' => [
            'Admin\AuthenticationAdapter' => 'Admin\Factory\AuthenticationAdapterFactory',
            'AdminErrorHandling'          => 'Admin\Factory\AdminErrorHandlingFactory',
            'AdministratorTable'          => 'Admin\Factory\AdministratorTableFactory',
            'ContentTable'                => 'Admin\Factory\ContentTableFactory',
            'LanguageTable'               => 'Admin\Factory\LanguageTableFactory',
            'MenuTable'                   => 'Admin\Factory\MenuTableFactory',
            'UserTable'                   => 'Admin\Factory\UserTableFactory',
            'TermTranslationTable'        => 'Admin\Factory\TermTranslationTableFactory',
            'TermCategoryTable'           => 'Admin\Factory\TermCategoryTableFactory',
            'TermTable'                   => 'Admin\Factory\TermTableFactory',
            'AdminMenuTable'              => 'Admin\Factory\AdminMenuTableFactory',
        ],
    ],
    'controllers' => [
        'factories' => [
            'Admin\Controller\Content'          => 'Admin\Factory\Controller\ContentFormFactory',
            'Admin\Controller\Menu'             => 'Admin\Factory\Controller\MenuFormFactory',
            'Admin\Controller\Term'             => 'Admin\Factory\Controller\TermFormFactory',
            'Admin\Controller\TermCategory'     => 'Admin\Factory\Controller\TermCategoryFormFactory',
            'Admin\Controller\Language'         => 'Admin\Factory\Controller\LanguageFormFactory',
            'Admin\Controller\Administrator'    => 'Admin\Factory\Controller\AdministratorFormFactory',
            'Admin\Controller\AdminMenu'        => 'Admin\Factory\Controller\AdminMenuFormFactory',
            'Admin\Controller\User'             => 'Admin\Factory\Controller\UserFormFactory',
        ],
        'invokables' => [
            'Admin\Controller\Index'            => 'Admin\Controller\IndexController',
            'Admin\Controller\TermTranslation'  => 'Admin\Controller\TermTranslationController',
        ],
    ],
    'view_manager' => [
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
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
