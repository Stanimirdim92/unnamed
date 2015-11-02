<?php return array (
  'router' =>
  array (
    'routes' =>
    array (
      'themes' =>
      array (
        'type' => 'Literal',
        'options' =>
        array (
          'route' => '/theme',
          'defaults' =>
          array (
            '__NAMESPACE__' => 'Themes\\Controller',
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
              'route' => '/[:controller[/:action]]',
              'constraints' =>
              array (
                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
              ),
            ),
          ),
        ),
      ),
    ),
  ),
  'controllers' =>
  array (
    'factories' =>
    array (
      'Themes\\Controller\\Index' => 'Themes\\Factory\\Controller\\IndexControllerFactory',
    ),
  ),
  'service_manager' =>
  array (
    'factories' =>
    array (
      'initThemes' => 'Themes\\Factory\\ThemesFactory',
      'getThemesFromDir' => 'Themes\\Factory\\GetThemesFromDir',
    ),
    'invokables' =>
    array (
      'reloadService' => 'Themes\\Service\\ReloadService',
    ),
  ),
  'theme' =>
  array (
    'name' => 'default',
  ),
  'view_manager' =>
  array (
    'template_path_stack' =>
    array (
      'themes' => 'C:\\xampp\\htdocs\\unnamed\\module\\Themes\\config/../view',
    ),
  ),
);
