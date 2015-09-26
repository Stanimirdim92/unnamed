<?php

/**
 * @copyright  2015 (c) Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 *
 * @version    0.0.14
 *
 * @link       TBA
 */

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
        'template_map' => include __DIR__ . '/../template_map.php',
    ],
];
