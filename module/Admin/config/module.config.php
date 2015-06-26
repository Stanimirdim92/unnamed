<?php
return [
    'service_manager' => [
       'abstract_factories' => [
        ],
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
            'AdminMenu'                   => 'Admin\Factory\AdminMenuFactory',
        ],
    ],
    'controllers' => [
        'factories' => [
            'Admin\Controller\Content' => "Admin\Factory\Controller\ContentFormFactory",
        ],
        'invokables' => [
            'Admin\Controller\Index'            => 'Admin\Controller\IndexController',
            'Admin\Controller\AdminMenu'        => 'Admin\Controller\AdminMenuController',
            'Admin\Controller\Term'             => 'Admin\Controller\TermController',
            'Admin\Controller\TermCategory'     => 'Admin\Controller\TermCategoryController',
            'Admin\Controller\TermTranslation'  => 'Admin\Controller\TermTranslationController',
            'Admin\Controller\User'             => 'Admin\Controller\UserController',
            'Admin\Controller\Administrator'    => 'Admin\Controller\AdministratorController',
            'Admin\Controller\Language'         => 'Admin\Controller\LanguageController',
            'Admin\Controller\Menu'             => 'Admin\Controller\MenuController',
            'ImagineService'                    => 'Imagine\Gd\Imagine',
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
            'error/404'               => __DIR__ . '/../../Admin/view/error/layout.phtml',
            'error/index'             => __DIR__ . '/../../Admin/view/error/index.phtml',
            'error/layout'            => __DIR__ . '/../../Admin/view/error/layout.phtml',
        ],
        'strategies' => [
            'ViewJsonStrategy',
        ],
    ],
];
