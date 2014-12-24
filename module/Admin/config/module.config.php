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
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => array(
            'error/404'               => __DIR__ . '/../../Admin/view/error/404.phtml',
            'error/index'             => __DIR__ . '/../../Admin/view/error/index.phtml',          
        ),
        'strategies' => array(
            'ViewJsonStrategy',
        ),
    ),
);