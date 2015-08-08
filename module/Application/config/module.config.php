<?php
return [
    'router' => [
        'routes' => [
            'application' => [
                'type'    => 'Segment',
                'options' => [
                    'route'    => '/[:controller[/][:action[/[:id][token/:token][:title]][search/:search]]]',
                    'constraints' => [
                        'controller' => '[a-zA-Z0-9_-]*',
                        'action'     => '[a-zA-Z0-9_-]*',
                        'token'      => '[[a-zA-Z-+_/&0-9]*',
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
                'may_terminate' => true,
            ],
            'news' => [
                'type' => 'Segment',
                'options' => [
                    'route'    => '/news[/][post/:post][page/:page]',
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
            ],
        ],
    ],
    'service_manager' => [
        'abstract_factories' => [
            'CacheAbstractFactory' => 'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
        ],
        'factories' => [
            'Application\Cache'       =>  'Zend\Cache\Service\StorageCacheFactory',
            'ApplicationErrorHandling' => 'Application\Factory\ApplicationErrorHandlingFactory',
            'ResetPasswordTable'       => 'Application\Factory\ResetPasswordTableFactory',
            'initSession'              => 'Application\Factory\ApplicationSessionFactory',
        ],
    ],
    'controllers' => [
        'factories' => [
            'Application\Controller\Login'        => 'Application\Factory\Controller\LoginFormFactory',
            'Application\Controller\Contact'      => 'Application\Factory\Controller\ContactControllerFactory',
            'Application\Controller\Registration' => 'Application\Factory\Controller\RegistrationFormFactory',
        ],
        'invokables' => [
            'Application\Controller\Index'        => 'Application\Controller\IndexController',
            'Application\Controller\News'         => 'Application\Controller\NewsController',
            'Application\Controller\Menu'         => 'Application\Controller\MenuController',
        ],
    ],
    'controller_plugins' => [
        'factories' => [
            'Mailing'               => 'Application\Controller\Plugin\Factory\MailingFactory',
            'UserData'              => 'Application\Controller\Plugin\Factory\UserDataFactory',
            'setLayoutMessages'     => 'Application\Controller\Plugin\Factory\LayoutMessagesFactory',
            'InitMetaTags'          => 'Application\Controller\Plugin\Factory\InitMetaTagsFactory',
            'getParam'              => 'Application\Controller\Plugin\Factory\GetUrlParamsFactory',
            'getTable'              => 'Application\Controller\Plugin\Factory\GetTableModelFactory',
            'getFunctions'          => 'Application\Controller\Plugin\Factory\FunctionsFactory',
            'setErrorCode'          => 'Application\Controller\Plugin\Factory\ErrorCodesFactory'
        ]
    ],
    'view_helpers' => [
        'invokables'=> [
            'translate' => 'Application\View\Helper\TranslateHelper',
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
