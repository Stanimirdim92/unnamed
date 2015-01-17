<?php
use Zend\Stdlib\ArrayUtils;

$config = array(
    // This should be an array of module namespaces used in the application.
    'modules' => array(
        'EdpModuleLayouts',
        'Admin',
        'Application',
    ),

    // These are various options for the listeners attached to the ModuleManager
    'module_listener_options' => array(
        // This should be an array of paths in which modules reside.
        // If a string key is provided, the listener will consider that a module
        // namespace, the value of that key the specific path to that module's
        // Module class.
        'module_paths' => array(
            './module',
            './vendor',
        ),

        // An array of paths from which to glob configuration files after
        // modules are loaded. These effectively override configuration
        // provided by modules themselves. Paths may use GLOB_BRACE notation.
        'config_glob_paths' => array(
            'config/autoload/{,*.}{global,local}.php',
        ),
    ),

    // Used to create an own service manager. May contain one or more child arrays.
    //'service_listener_options' => array(
    //     array(
    //         'service_manager' => $stringServiceManagerName,
    //         'config_key'      => $stringConfigKey,
    //         'interface'       => $stringOptionalInterface,
    //         'method'          => $stringRequiredMethodName,
    //     ),
    // )

   // Initial configuration with which to seed the ServiceManager.
   // Should be compatible with Zend\ServiceManager\Config.
   // 'service_manager' => array(),
);

$cacheConfig = array(
    // Whether or not to enable a configuration cache.
    // If enabled, the merged configuration will be cached and used in
    // subsequent requests.
    'config_cache_enabled' => true,

    // The key used to create the configuration cache file name.
    // configCache
    'config_cache_key' => '9a8d8e1c3d47149e3193782e1f5b5d1f',

    // Whether or not to enable a module class map cache.
    // If enabled, creates a module class map cache which will be used
    // by in future requests, to reduce the autoloading process.
    'module_map_cache_enabled' => true,

    // The key used to create the class map cache file name.
    // moduleCache
    'module_map_cache_key' => '1cf4c26ec62554e65b00134eed95a538',

    // The path in which to cache merged configuration.
    'cache_dir' => 'data/cache/',

    // Whether or not to enable modules dependency checking.
    // Enabled by default, prevents usage of modules that depend on other modules
    // that weren't loaded.
    'check_dependencies' => true,
);

// We want to activate the cache only in production environment
if (getenv('APPLICATION_ENV') == 'development')
{
    $config = ArrayUtils::merge($config, $cacheConfig);
}

return $config;