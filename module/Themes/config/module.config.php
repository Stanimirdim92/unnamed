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
              'defaults' => 
              array (
                '__NAMESPACE__' => 'Themes\\Controller',
                'controller' => 'Index',
                'action' => 'index',
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
  ),
  'theme' => 
  array (
    'name' => 'awesome',
  ),
  'view_manager' => 
  array (
    'template_map' => 
    array (
      'themes/index/index' => 'C:\\xampp\\htdocs\\unnamed\\module\\Themes/view/themes/index/index.phtml',
      'error/index' => 'C:\\xampp\\htdocs\\unnamed\\module\\Themes/view/error/index.phtml',
      'layout/layout' => 'C:\\xampp\\htdocs\\unnamed\\module\\Themes/view/layout/layout.phtml',
    ),
  ),
);