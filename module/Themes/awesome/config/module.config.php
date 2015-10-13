<?php

return [
    'description' => 'Application Awesome Theme',
    'screenshot' => '',
    'author' => 'Stanimir Dimitrov',
    'template_map' => include __DIR__ . '/../template_map.php',
    'template_path_stack' => [
        'awesome' => __DIR__ . '/../view',
    ],
];
