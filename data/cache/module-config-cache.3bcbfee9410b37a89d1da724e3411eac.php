<?php
return array (
  'router' => 
  array (
    'routes' => 
    array (
      'application' => 
      array (
        'type' => 'Literal',
        'options' => 
        array (
          'route' => '/',
          'defaults' => 
          array (
            '__NAMESPACE__' => 'Application\\Controller',
            'controller' => 'Index',
            'action' => 'index',
          ),
        ),
        'may_terminate' => true,
        'child_routes' => 
        array (
          'default' => 
          array (
            'type' => 'Segment',
            'options' => 
            array (
              'route' => '[:controller[/][:action[/[:id][token/:token][:title]][search/:search]]]',
              'constraints' => 
              array (
                'controller' => '[a-zA-Z0-9_-]*',
                'action' => '[a-zA-Z0-9_-]*',
                'token' => '[a-zA-Z-+_/&0-9]*',
                'title' => '[a-zA-Z0-9_-]*',
                'search' => '[a-zA-Z0-9_-]*',
                'id' => '[0-9]+',
              ),
              'defaults' => 
              array (
                '__NAMESPACE__' => 'Application\\Controller',
                'controller' => 'Index',
                'action' => 'index',
              ),
            ),
          ),
        ),
      ),
      'news' => 
      array (
        'type' => 'Literal',
        'options' => 
        array (
          'route' => '/news',
          'constraints' => 
          array (
            'post' => '[a-zA-Z0-9_-]*',
            'page' => '[0-9]+',
          ),
          'defaults' => 
          array (
            '__NAMESPACE__' => 'Application\\Controller',
            'controller' => 'News',
            'action' => 'post',
          ),
        ),
        'may_terminate' => true,
        'child_routes' => 
        array (
          'default' => 
          array (
            'type' => 'Segment',
            'options' => 
            array (
              'route' => '/[post/:post][page/:page]',
              'constraints' => 
              array (
                'post' => '[a-zA-Z0-9_-]*',
                'page' => '[0-9]+',
              ),
              'defaults' => 
              array (
                '__NAMESPACE__' => 'Application\\Controller',
                'controller' => 'News',
                'action' => 'post',
              ),
            ),
          ),
        ),
      ),
      'admin' => 
      array (
        'type' => 'Literal',
        'options' => 
        array (
          'route' => '/admin',
          'defaults' => 
          array (
            '__NAMESPACE__' => 'Admin\\Controller',
            'controller' => 'Index',
            'action' => 'index',
          ),
        ),
        'may_terminate' => true,
        'child_routes' => 
        array (
          'default' => 
          array (
            'type' => 'Segment',
            'options' => 
            array (
              'route' => '/[:controller[/][:action[/][:id][/page/:page][/search/:search]]]',
              'constraints' => 
              array (
                'controller' => '[a-zA-Z0-9_-]*',
                'action' => '[a-zA-Z0-9_-]*',
                'search' => '[a-zA-Z0-9_-]*',
                'id' => '[0-9]+',
                'page' => '[0-9]+',
              ),
              'defaults' => 
              array (
                '__NAMESPACE__' => 'Admin\\Controller',
                'controller' => 'Index',
                'action' => 'index',
              ),
            ),
          ),
        ),
      ),
    ),
  ),
  'service_manager' => 
  array (
    'factories' => 
    array (
      'ApplicationErrorHandling' => 'Application\\Factory\\ApplicationErrorHandlingFactory',
      'ResetPasswordTable' => 'Application\\Factory\\Model\\ResetPasswordTableFactory',
      'initSession' => 'Application\\Factory\\ApplicationSessionFactory',
      'Zend\\Db\\Adapter\\Adapter' => 'Application\\Factory\\AdapterServiceFactory',
      'AdminErrorHandling' => 'Admin\\Factory\\AdminErrorHandlingFactory',
      'AdministratorTable' => 'Admin\\Factory\\Model\\AdministratorTableFactory',
      'ContentTable' => 'Admin\\Factory\\Model\\ContentTableFactory',
      'LanguageTable' => 'Admin\\Factory\\Model\\LanguageTableFactory',
      'MenuTable' => 'Admin\\Factory\\Model\\MenuTableFactory',
      'UserTable' => 'Admin\\Factory\\Model\\UserTableFactory',
      'AdminMenuTable' => 'Admin\\Factory\\Model\\AdminMenuTableFactory',
      'translator' => 'Zend\\Mvc\\Service\\TranslatorServiceFactory',
    ),
    'abstract_factories' => 
    array (
      'CacheAbstractFactory' => 'Zend\\Cache\\Service\\StorageCacheAbstractServiceFactory',
    ),
  ),
  'controllers' => 
  array (
    'factories' => 
    array (
      'Application\\Controller\\Login' => 'Application\\Factory\\Controller\\LoginControllerFactory',
      'Application\\Controller\\Contact' => 'Application\\Factory\\Controller\\ContactControllerFactory',
      'Application\\Controller\\Registration' => 'Application\\Factory\\Controller\\RegistrationControllerFactory',
      'Admin\\Controller\\Content' => 'Admin\\Factory\\Controller\\ContentControllerFactory',
      'Admin\\Controller\\Menu' => 'Admin\\Factory\\Controller\\MenuControllerFactory',
      'Admin\\Controller\\Language' => 'Admin\\Factory\\Controller\\LanguageControllerFactory',
      'Admin\\Controller\\Administrator' => 'Admin\\Factory\\Controller\\AdministratorControllerFactory',
      'Admin\\Controller\\AdminMenu' => 'Admin\\Factory\\Controller\\AdminMenuControllerFactory',
      'Admin\\Controller\\User' => 'Admin\\Factory\\Controller\\UserControllerFactory',
    ),
    'invokables' => 
    array (
      'Application\\Controller\\Index' => 'Application\\Controller\\IndexController',
      'Application\\Controller\\News' => 'Application\\Controller\\NewsController',
      'Application\\Controller\\Menu' => 'Application\\Controller\\MenuController',
      'Admin\\Controller\\Index' => 'Admin\\Controller\\IndexController',
    ),
  ),
  'view_manager' => 
  array (
    'template_map' => 
    array (
      'application/contact/index' => 'C:\\xampp\\htdocs\\ZendBoilerplate\\module\\Application/view/application/contact/index.phtml',
      'application/index/index' => 'C:\\xampp\\htdocs\\ZendBoilerplate\\module\\Application/view/application/index/index.phtml',
      'application/login/index' => 'C:\\xampp\\htdocs\\ZendBoilerplate\\module\\Application/view/application/login/index.phtml',
      'application/login/newpassword' => 'C:\\xampp\\htdocs\\ZendBoilerplate\\module\\Application/view/application/login/newpassword.phtml',
      'application/login/resetpassword' => 'C:\\xampp\\htdocs\\ZendBoilerplate\\module\\Application/view/application/login/resetpassword.phtml',
      'application/menu/title' => 'C:\\xampp\\htdocs\\ZendBoilerplate\\module\\Application/view/application/menu/title.phtml',
      'application/news/post' => 'C:\\xampp\\htdocs\\ZendBoilerplate\\module\\Application/view/application/news/post.phtml',
      'application/pagination' => 'C:\\xampp\\htdocs\\ZendBoilerplate\\module\\Application/view/application/pagination.phtml',
      'application/registration/index' => 'C:\\xampp\\htdocs\\ZendBoilerplate\\module\\Application/view/application/registration/index.phtml',
      'error/index' => 'C:\\xampp\\htdocs\\ZendBoilerplate\\module\\Admin/view/error/index.phtml',
      'layout/layout' => 'C:\\xampp\\htdocs\\ZendBoilerplate\\module\\Application/view/layout/layout.phtml',
      'admin/admin-menu/add' => 'C:\\xampp\\htdocs\\ZendBoilerplate\\module\\Admin/view/admin/admin-menu/add.phtml',
      'admin/admin-menu/detail' => 'C:\\xampp\\htdocs\\ZendBoilerplate\\module\\Admin/view/admin/admin-menu/detail.phtml',
      'admin/admin-menu/index' => 'C:\\xampp\\htdocs\\ZendBoilerplate\\module\\Admin/view/admin/admin-menu/index.phtml',
      'admin/admin-menu/modify' => 'C:\\xampp\\htdocs\\ZendBoilerplate\\module\\Admin/view/admin/admin-menu/modify.phtml',
      'admin/administrator/add' => 'C:\\xampp\\htdocs\\ZendBoilerplate\\module\\Admin/view/admin/administrator/add.phtml',
      'admin/administrator/index' => 'C:\\xampp\\htdocs\\ZendBoilerplate\\module\\Admin/view/admin/administrator/index.phtml',
      'admin/administrator/modify' => 'C:\\xampp\\htdocs\\ZendBoilerplate\\module\\Admin/view/admin/administrator/modify.phtml',
      'admin/content/add' => 'C:\\xampp\\htdocs\\ZendBoilerplate\\module\\Admin/view/admin/content/add.phtml',
      'admin/content/detail' => 'C:\\xampp\\htdocs\\ZendBoilerplate\\module\\Admin/view/admin/content/detail.phtml',
      'admin/content/imgupload' => 'C:\\xampp\\htdocs\\ZendBoilerplate\\module\\Admin/view/admin/content/imgupload.phtml',
      'admin/content/index' => 'C:\\xampp\\htdocs\\ZendBoilerplate\\module\\Admin/view/admin/content/index.phtml',
      'admin/content/modify' => 'C:\\xampp\\htdocs\\ZendBoilerplate\\module\\Admin/view/admin/content/modify.phtml',
      'admin/index/index' => 'C:\\xampp\\htdocs\\ZendBoilerplate\\module\\Admin/view/admin/index/index.phtml',
      'admin/language/add' => 'C:\\xampp\\htdocs\\ZendBoilerplate\\module\\Admin/view/admin/language/add.phtml',
      'admin/language/detail' => 'C:\\xampp\\htdocs\\ZendBoilerplate\\module\\Admin/view/admin/language/detail.phtml',
      'admin/language/index' => 'C:\\xampp\\htdocs\\ZendBoilerplate\\module\\Admin/view/admin/language/index.phtml',
      'admin/language/modify' => 'C:\\xampp\\htdocs\\ZendBoilerplate\\module\\Admin/view/admin/language/modify.phtml',
      'admin/language/translations' => 'C:\\xampp\\htdocs\\ZendBoilerplate\\module\\Admin/view/admin/language/translations.phtml',
      'admin/menu/add' => 'C:\\xampp\\htdocs\\ZendBoilerplate\\module\\Admin/view/admin/menu/add.phtml',
      'admin/menu/detail' => 'C:\\xampp\\htdocs\\ZendBoilerplate\\module\\Admin/view/admin/menu/detail.phtml',
      'admin/menu/index' => 'C:\\xampp\\htdocs\\ZendBoilerplate\\module\\Admin/view/admin/menu/index.phtml',
      'admin/menu/modify' => 'C:\\xampp\\htdocs\\ZendBoilerplate\\module\\Admin/view/admin/menu/modify.phtml',
      'admin/pagination' => 'C:\\xampp\\htdocs\\ZendBoilerplate\\module\\Admin/view/admin/pagination.phtml',
      'admin/user/detail' => 'C:\\xampp\\htdocs\\ZendBoilerplate\\module\\Admin/view/admin/user/detail.phtml',
      'admin/user/disabled' => 'C:\\xampp\\htdocs\\ZendBoilerplate\\module\\Admin/view/admin/user/disabled.phtml',
      'admin/user/index' => 'C:\\xampp\\htdocs\\ZendBoilerplate\\module\\Admin/view/admin/user/index.phtml',
      'admin/user/modify' => 'C:\\xampp\\htdocs\\ZendBoilerplate\\module\\Admin/view/admin/user/modify.phtml',
      'layout/admin' => 'C:\\xampp\\htdocs\\ZendBoilerplate\\module\\Admin/view/layout/admin.phtml',
    ),
    'display_not_found_reason' => false,
    'display_exceptions' => false,
    'doctype' => 'HTML5',
    'not_found_template' => 'error/index',
    'exception_template' => 'error/index',
    'default_template_suffix' => 'phtml',
    'strategies' => 
    array (
      0 => 'ViewJsonStrategy',
    ),
  ),
  'form_elements' => 
  array (
    'factories' => 
    array (
      'Admin\\Form\\ContentForm' => 'Admin\\Factory\\Form\\ContentFormFactory',
      'Admin\\Form\\MenuForm' => 'Admin\\Factory\\Form\\MenuFormFactory',
      'Admin\\Form\\AdminMenuForm' => 'Admin\\Factory\\Form\\AdminMenuFormFactory',
    ),
  ),
  'controller_plugins' => 
  array (
    'factories' => 
    array (
      'translate' => 'Application\\Controller\\Plugin\\Factory\\TranslateFactory',
      'Mailing' => 'Application\\Controller\\Plugin\\Factory\\MailingFactory',
      'UserData' => 'Application\\Controller\\Plugin\\Factory\\UserDataFactory',
      'setLayoutMessages' => 'Application\\Controller\\Plugin\\Factory\\LayoutMessagesFactory',
      'InitMetaTags' => 'Application\\Controller\\Plugin\\Factory\\InitMetaTagsFactory',
      'getParam' => 'Application\\Controller\\Plugin\\Factory\\GetUrlParamsFactory',
      'getTable' => 'Application\\Controller\\Plugin\\Factory\\GetTableModelFactory',
      'getFunctions' => 'Application\\Controller\\Plugin\\Factory\\FunctionsFactory',
      'setErrorCode' => 'Application\\Controller\\Plugin\\Factory\\ErrorCodesFactory',
    ),
  ),
  'translator' => 
  array (
    'locale' => 'en',
    'translation_file_patterns' => 
    array (
      0 => 
      array (
        'base_dir' => 'C:\\xampp\\htdocs\\ZendBoilerplate\\config\\autoload/../../module/Application/languages/phpArray',
        'type' => 'phpArray',
        'pattern' => '%s.php',
      ),
    ),
    'cache' => 
    array (
      'adapter' => 
      array (
        'name' => 'Filesystem',
        'options' => 
        array (
          'cache_dir' => 'C:\\xampp\\htdocs\\ZendBoilerplate\\config\\autoload/../../data/cache/frontend',
          'ttl' => '3600',
        ),
      ),
      'plugins' => 
      array (
        0 => 
        array (
          'name' => 'serializer',
          'options' => 
          array (
          ),
        ),
        'exception_handler' => 
        array (
          'throw_exceptions' => false,
        ),
      ),
    ),
  ),
  'db' => 
  array (
    'driver' => 'pdo',
    'port' => '',
    'dsn' => 'mysql:dbname=xj;host=',
    'driver_options' => 
    array (
      20 => false,
      1002 => 'SET NAMES "UTF8"',
    ),
    'username' => 'root',
    'password' => '',
  ),
  'zenddevelopertools' => 
  array (
    'profiler' => 
    array (
      'enabled' => true,
      'strict' => true,
      'flush_early' => false,
      'cache_dir' => 'data/cache',
      'matcher' => 
      array (
      ),
      'collectors' => 
      array (
      ),
    ),
    'events' => 
    array (
      'enabled' => true,
      'collectors' => 
      array (
      ),
      'identifiers' => 
      array (
      ),
    ),
    'toolbar' => 
    array (
      'enabled' => true,
      'auto_hide' => false,
      'position' => 'bottom',
      'version_check' => true,
      'entries' => 
      array (
      ),
    ),
  ),
);