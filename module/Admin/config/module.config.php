<?php

/**
 * @copyright  2015 (c) Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 *
 * @version    0.0.18
 *
 * @link       TBA
 */

namespace Admin;

return [
    'router' => [
        'routes' => [
            'admin' => [
                'type'    => 'Zend\Mvc\Router\Http\Literal',
                'options' => [
                    'route' => '/admin',
                    'defaults' => [
                        'controller' => 'Admin\Controller\Index',
                        'action'     => 'index',
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'default' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/[:controller[/][:action[/][[:id][:themeName]][page/:page][/search/:search]]]',
                            'constraints' => [
                                'controller' => '[a-zA-Z0-9_-]*',
                                'action'     => '[a-zA-Z0-9_-]*',
                                'search'     => '[a-zA-Z0-9_-]*',
                                'id'         => '[0-9]+',
                                'themeName'         => '[a-zA-Z0-9_-]*',
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
            'Admin\Controller\Content'       => Factory\Controller\ContentControllerFactory::class,
            'Admin\Controller\Menu'          => Factory\Controller\MenuControllerFactory::class,
            'Admin\Controller\Language'      => Factory\Controller\LanguageControllerFactory::class,
            'Admin\Controller\Administrator' => Factory\Controller\AdministratorControllerFactory::class,
            'Admin\Controller\AdminMenu'     => Factory\Controller\AdminMenuControllerFactory::class,
            'Admin\Controller\User'          => Factory\Controller\UserControllerFactory::class,
            'Admin\Controller\Settings'      => Factory\Controller\SettingsControllerFactory::class,
        ],
        'invokables' => [
            'Admin\Controller\Index'  => Controller\IndexController::class,
            'Admin\Controller\Themes' => Controller\ThemesController::class,
        ],
    ],
    'form_elements' => [
        'factories' => [
            'Admin\Form\ContentForm'              => Factory\Form\ContentFormFactory::class,
            'Admin\Form\MenuForm'                 => Factory\Form\MenuFormFactory::class,
            'Admin\Form\AdminMenuForm'            => Factory\Form\AdminMenuFormFactory::class,
            'Admin\Form\SettingsMailForm'         => Factory\Form\SettingsMailFormFactory::class,
            'Admin\Form\SettingsPostsForm'        => Factory\Form\SettingsPostsFormFactory::class,
            'Admin\Form\SettingsGeneralForm'      => Factory\Form\SettingsGeneralFormFactory::class,
            'Admin\Form\SettingsDiscussionForm'   => Factory\Form\SettingsDiscussionFormFactory::class,
            'Admin\Form\SettingsRegistrationForm' => Factory\Form\SettingsRegistrationFormFactory::class,
        ],
    ],
    'shared' => [
        'Admin\Controller\Themes'             => false,
        'Admin\Controller\Content'            => false,
        'Admin\Controller\Index'              => false,
        'Admin\Controller\Menu'               => false,
        'Admin\Controller\Language'           => false,
        'Admin\Controller\Administrator'      => false,
        'Admin\Controller\AdminMenu'          => false,
        'Admin\Controller\User'               => false,
        'Admin\Controller\Settings'           => false,
        'Admin\Form\ContentForm'              => false,
        'Admin\Form\MenuForm'                 => false,
        'Admin\Form\AdminMenuForm'            => false,
        'Admin\Form\SettingsMailForm'         => false,
        'Admin\Form\SettingsPostsForm'        => false,
        'Admin\Form\SettingsGeneralForm'      => false,
        'Admin\Form\SettingsDiscussionForm'   => false,
        'Admin\Form\SettingsRegistrationForm' => false,
    ],
    'view_manager' => [
        'template_map' => include __DIR__ . '/../template_map.php',
    ],
];
