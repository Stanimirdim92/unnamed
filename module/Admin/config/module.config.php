<?php
return array(
    'service_manager' => array(
        'factories' => array(
            'Admin\AuthenticationAdapter' => 'Admin\Factory\AuthenticationAdapterFactory',
        ),
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/layout',
        'exception_template'       => 'error/index',
        // This can be used as the default suffix for template scripts resolving, it defaults to 'phtml'.
        // 'default_template_suffix' => 'php',
        'template_map' => array(
            'error/404'               => __DIR__ . '/../../Admin/view/error/layout.phtml',
            'error/index'             => __DIR__ . '/../../Admin/view/error/index.phtml',
            'error/layout'            => __DIR__ . '/../../Admin/view/error/layout.phtml',
        ),
    ),
);
