<?php

return [
    'description' => 'Application Default Theme',
    'screenshot' => '',
    'author' => 'Stanimir Dimitrov',
    'template_map' => include __DIR__ . '/../template_map.php',
    'template_path_stack' => [
        'default' => __DIR__ . '/../view',
    ],
];
