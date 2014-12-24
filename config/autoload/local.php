<?php
return array(
    'db' => array(
        'driver' => 'Pdo_Mysql',
        'dsn' => 'mysql:dbname=xj;host=localhost',
        'driver_options' => array(PDO::ATTR_EMULATE_PREPARES => false,
                                  PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''),
        'username' => 'root',
        'password' => '',
    ),
    'admin' => array(
        'auth_adapter' => array(
            'config' => array(
                'accept_schemes' => 'basic',
                'realm'          => 'admin',
                'digest_domains' => '/admin',
                'nonce_timeout'  => 3600,
                'proxy_auth'     => false,
            ),
            'basic_passwd_file' => __DIR__ . '/real/basic_passwd.txt',
        ),
    ),
);