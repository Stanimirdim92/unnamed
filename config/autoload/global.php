<?php
return [
    'service_manager' => [
        'factories' => [
            'Zend\Db\Adapter\Adapter' => 'Application\Factory\AdapterServiceFactory',
        ],
    ],
];
