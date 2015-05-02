<?php
return array(
    'router' => array(
        'routes' => array(
            'home' => array(
                'type' => 'Literal',
                'options' => array(
                    'route'    => '[/]',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Index',
                        'action'     => 'index',
                    ),
                ),
            ),
            'application' => array(
                'type'    => 'Segment',
                'options' => array(
                    'route' => '/[:controller[/][:action[/id/:id][/page/:page][/search/:search]]]',
                    'constraints' => array(
                        'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'         => '[0-9]+',
                        'page'       => '[0-9]+',
                        'search'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller'    => 'Index',
                        'action'        => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes'  => array(
                    'default'   => array(
                        'type'    => 'Wildcard',
                        'options' => array(
                        ),
                    ),
                ),
            ),
            'contact'     => array(
                'type'    => 'Segment',
                'options' => array(
                    'route'    => '/contact',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Index',
                        'action'     => 'contact',
                    ),
                ),
            ),
            'news' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/news[/][post/:post][/page/:page]',
                    'constraints' => array(
                        'post' => '[a-zA-Z][a-zA-Z0-9_-]+',
                        'page' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Application\Controller\News',
                        'action'     => 'news',
                    ),
                ),
            ),
            'menu' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/menu[/][:title]',
                    'constraints' => array(
                        'title' => '[a-zA-Z][a-zA-Z0-9_-]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Application\Controller\Menu',
                        'action'     => 'menu',
                    ),
                ),
            ),
            'admin' => array(
                'type'    => 'Segment',
                'options' => array(
                    'route' => '/admin[/][:controller[/][:action[/id/:id][/page/:page][/search/:search]]]',
                    'constraints' => array(
                        'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'         => '[0-9]+',
                        'page'       => '[0-9]+',
                        'search'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        '__NAMESPACE__' => 'Admin\Controller',
                        'controller' => 'Index',
                        'action'     => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'default' => array(
                        'type'    => 'Wildcard',
                        'options' => array(
                        ),
                    ),
                ),
            ),
        ),
    ),
    'service_manager' => array(
        'abstract_factories' => array(
        ),
        'factories' => array(
        ),
    ),
    'controllers' => array(
        'factories' => array(
            'ApplicationErrorHandling' => 'Application\Factory\ApplicationErrorHandlingFactory',
            'ResetPasswordTable'       => "Application\Factory\ResetPasswordTableFactory",
        ),
        'invokables' => array(
            'Application\Controller\Index'        => 'Application\Controller\IndexController',
            'Application\Controller\Login'        => 'Application\Controller\LoginController',
            'Application\Controller\Registration' => 'Application\Controller\RegistrationController',
            'Application\Controller\Profile'      => 'Application\Controller\ProfileController',
            'Application\Controller\News'         => 'Application\Controller\NewsController',
            'Application\Controller\Menu'         => 'Application\Controller\MenuController',
        ),
    ),
    'view_helpers' => array(
        'factories' => array(
        ),
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/layout',
        'exception_template'       => 'error/index',
        // This can be used as the default suffix for template scripts resolving, it defaults to 'phtml'.
        // 'default_template_suffix' => 'php',
        'template_map' => array(
            'application/layout'      => __DIR__ . '/../../Application/view/layout/layout.phtml',
            'error/layout'            => __DIR__ . '/../../Application/view/error/layout.phtml',
            'error/index'             => __DIR__ . '/../../Application/view/error/index.phtml',
            'error/404'               => __DIR__ . '/../../Application/view/error/index.phtml',
        ),
        'template_path_stack' => array(
            'Application'     => __DIR__ . '/../../Application/view',
            'Admin'           => __DIR__ . '/../../Admin/view',
        ),
        'strategies' => array(
            'ViewJsonStrategy',
        ),
    ),
    'module_layouts' => array(
        'Application' => 'layout/layout',
        'Admin'       => 'layout/admin',
    ),
);
