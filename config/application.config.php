<?php

$modules = [];

if (APP_ENV === 'development') {
    $modules[] = 'ZendDeveloperTools';
    $modules[] = 'BjyProfiler';
    $modules[] = 'SanSessionToolbar';
}

$modules[] = 'Application';
$modules[] = 'Admin';
$modules[] = 'Themes';

return [
    'modules' => $modules,

    'module_listener_options' => [
        'module_paths' => [
            './module',
            './vendor',
        ],

        'config_glob_paths' => [
            'config/autoload/{,*.}{global,local}.php',
        ],

        'config_cache_enabled' => (APP_ENV === 'production'),
        'config_cache_key' => 'app_config',
        'module_map_cache_enabled' => (APP_ENV === 'production'),
        'module_map_cache_key' => 'module_map',
        'cache_dir' => dirname(__DIR__)."/data/cache/modules",
        'check_dependencies' => (APP_ENV !== 'production'),
    ],
];
