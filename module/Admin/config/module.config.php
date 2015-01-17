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
        'not_found_template'       => 'layout/error-layout',
        'exception_template'       => 'layout/error-layout',
        'template_map' => array(
            'error/404'               => __DIR__ . '/../../Application/view/layour/error-layout.phtml',
            'error/index'             => __DIR__ . '/../../Application/view/layour/error-layout.phtml',
            'layout/error-layout'     => __DIR__ . '/../../Application/view/layout/error-layout.phtml',    
        ),
        'strategies' => array(
            'ViewJsonStrategy',
        ),
    ),
);