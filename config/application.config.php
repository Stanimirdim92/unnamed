<?php

$modules = [];

if (APP_ENV === 'development') {
    $modules[] = 'ZendDeveloperTools';
    $modules[] = 'BjyProfiler';
    $modules[] = 'SanSessionToolbar';
}

$modules[] = 'Application';
$modules[] = 'Admin';

return [
    'modules' => $modules,

    'module_listener_options' => [
        'module_paths' => [
            './module',
            './vendor',
        ],

        'config_glob_paths' => [
            // 'config/autoload/{{,*.}global,{,*.}local}.php',
            'config/autoload/{,*.}{global,local}.php',
        ],

        'config_cache_enabled' => (APP_ENV === 'production'),
        'config_cache_key' => md5('app_config'),
        'module_map_cache_enabled' => (APP_ENV === 'production'),
        'module_map_cache_key' => md5('module_map'),
        'cache_dir' => dirname(__DIR__)."/data/cache",
        'check_dependencies' => (APP_ENV !== 'production'),
    ],
];
