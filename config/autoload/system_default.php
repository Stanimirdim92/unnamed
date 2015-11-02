<?php

/**
 * @copyright  2015 (c) Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 *
 * @version    0.0.21
 *
 * @link       TBA
 */

/**
 * Default system config.
 * Upon system installation, a new file system.local.php
 * will be created, which will stay on the server
 * and never be exposed to public
 */
return [
    'system_config' => [
        'general' => [
            'site_name' => 'Unnamed',
            'site_tag_line' => 'Flexible, strong and fast CMS built on top of Zend Framework 2',
            'site_description' => 'Flexible, strong and fast CMS built on top of Zend Framework 2',
            'site_keywords' => 'Flexible, strong, fast, CMS, ZF2',
            'site_text' => 'Flexible, strong and fast CMS built on top of Zend Framework 2',
            'system_email' => 'john@example.com',
            'timezone' => 'UTC',
            'robots_indexing' => 0,
            'date_format' => 'F j, Y',
            'time_format' => 'H:i:s',
            'date_formats' => [
                'F j, Y' => 'F j, Y',
                'Y-m-d' => 'Y-m-d',
                'm/d/Y' => 'm/d/Y',
                'd/m/Y' => 'd/m/Y',
            ],
            'time_formats' => [
                'g:i a' => 'g:i a',
                'g:i A' => 'g:i A',
                'H:i' => 'H:i',
                'H:i:s' => 'H:i:s',
            ],
        ],
        'registration' => [
            'allow_registrations' => 1,
            'email_verification' => 'none',
            'email_verifications' => [
                'none' => 'none',
                'email' => 'email',
                'admin' => 'admin',
            ],
        ],
        'mail' => [
            'host' => 'smtp.gmail.com',
            'name' => 'localhost',
            'port' => 465,
            'from' => 'noreplay@example.com',
            'connection_class'  => 'login',
            'username' => 'user',
            'password' => 'pass',
            'ssl' => 'ssl',
            'connection_classes'  => [
                'smtp' => 'smtp',
                'login' => 'login',
                'plain' => 'plain',
                'crammd5' => 'crammd5',
            ],
        ],
        'posts' => [
            'content' => 50,
            'menu' => 50,
            'adminmenu' => 50,
            'administrator' => 50,
            'language' => 50,
            'user' => 50,
            'news' => 10,
        ],
        'discussion' => [
            'allow_comments' => 1,
            'comment_registration' => 1,
            'comments_notify' => 1,
            'moderation_notify' => 1,
            'comment_moderation' => 0,
            'show_avatars' => 1,
        ],
    ],
];
