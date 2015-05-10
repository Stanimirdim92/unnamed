<?php
return [
    'service_manager' => [
        'factories' => [
            'Zend\Db\Adapter\Adapter' => '\Admin\Factory\AdapterServiceFactory',
        ],
    ],
];
