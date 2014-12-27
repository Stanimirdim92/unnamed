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
                    'route' => '/[:controller[/][:action[/id/:id][/page/:page]]]',
                    'constraints' => array(
                        'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'         => '[0-9]+',
                        'page'       => '[0-9]+',
                    ),
                    'defaults' => array(
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller'    => 'Index',
                        'action'        => 'index',
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
            'urlFriendlyPages' => array(
                'type'    => 'Segment', // MUST BE SEGMENT. DO NOT CHANGE OR IT WILL NOT WORK!
                'options' => array(
                    'route'    => '/:param[/]',
                    'constraints' => array(
                        // 'regex'     => '/^\d{4},(?:\s|\w)+/u*', // unicode baby!
                        'param'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Application\Controller\Index',
                        'action'        => 'page'
                    ),
                ),
            ),
            'contact' => array(
                'type' => 'Segment',
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
                    'route' => '/news[/][post/:post][page/:page]',
                    'constraints' => array(
                        'page' => '[0-9]+',
                        'post' => '[a-zA-Z][a-zA-Z0-9_-]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Application\Controller\News',
                        'action'     => 'news',
                    ),
                ),
            ),
            'login' => array(
                'type'    => 'Segment',
                'options' => array(
                    'route' => '/login',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Login',
                        'action'        => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'logout' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route'    => '/login/logout',
                            'defaults' => array(
                                'controller' => 'Application\Controller\Login',
                                'action'     => 'logout',
                            ),
                        ),
                    ),
                    'processlogin' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route'    => '/login/processlogin',
                            'defaults' => array(
                                'controller' => 'Application\Controller\Login',
                                'action'     => 'processlogin',
                            ),
                        ),
                    ),
                    'resetpassword' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route'    => '/login/resetpassword',
                            'defaults' => array(
                                'controller' => 'Application\Controller\Login',
                                'action'     => 'resetpassword',
                            ),
                        ),
                    ),
                    'newpasswordprocess' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route'    => '/login/newpasswordprocess',
                            'defaults' => array(
                                'controller' => 'Application\Controller\Login',
                                'action'     => 'newpasswordprocess',
                            ),
                        ),
                    ),
                ),
            ),
            'profile' => array(
                'type'    => 'Segment',
                'options' => array(
                    'route'    => '/profile',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Profile',
                        'action'        => 'settings',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'settings' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route'    => '/profile/settings',
                            'defaults' => array(
                                'controller' => 'Application\Controller\Profile',
                                'action'     => 'settings',
                            ),
                        ),
                    ),
                ),
            ),
            'registration' => array(
                'type'    => 'Segment',
                'options' => array(
                    'route' => '/registration',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Registration',
                        'action'        => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'processregistration' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route'    => '/registration/processregistration',
                            'defaults' => array(
                                'controller' => 'Application\Controller\Registration',
                                'action'     => 'processregistration',
                            ),
                        ),
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
                        'id' => '[0-9]+',
                        'page' => '[0-9]+',
                        'search' => '[a-zA-Z][a-zA-Z0-9_-]*'
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
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'layout/error-layout',
        'exception_template'       => 'layout/error-layout',
        // This will be used as the default suffix for template scripts resolving, it defaults to 'phtml'.
        // 'default_template_suffix' => 'php',
        'template_map' => array(
            'application/layout'      => __DIR__ . '/../../Application/view/layout/layout.phtml',
            'layout/error-layout'     => __DIR__ . '/../../Application/view/layout/error-layout.phtml',
        ),
        'template_path_stack' => array(
            'Admin'         => __DIR__ . '/../../Admin/view',
            'Application'   => __DIR__ . '/../../Application/view',
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
