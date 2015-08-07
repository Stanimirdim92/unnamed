<?php

chdir(dirname(__DIR__));

if (is_file('vendor/autoload.php')) {
    header( 'Content-Type: text/html; charset=utf-8' );
    die("autoload.php already exists. Skipping phar extraction as presumably it's already extracted.");
} else {
    $composerPhar = new Phar("Composer.phar");
    //php.ini setting phar.readonly must be set to 0
    $composerPhar->extractTo("vendor/");
}

/**
 * This requires the phar to have been extracted successfully.
 */
require 'vendor/autoload.php';

/**
 * Use the Composer classes
 */
use Composer\Console\Application;
use Composer\Command\UpdateCommand;
use Symfony\Component\Console\Input\ArrayInput;

/**
 * Run the app
 */
Composer\Console\Application::run(new ArrayInput(['command' => 'update']));
