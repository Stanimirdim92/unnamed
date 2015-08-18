<?php

/**
 * Silence every error report
 */
error_reporting(E_ALL);

ini_set("log_errors", 1);

/**
 * Display of all other errors
 */
ini_set("display_errors", 1);

/**
 * Display of all startup errors
 */
ini_set("display_startup_errors", 1);

/**
 * Temporary increase execution time
 */
set_time_limit(500);

/**
 * Change dir to point to root folder
 */
chdir(dirname(__DIR__));

if (is_file("composerInstalation/vendor/autoload.php")) {
    echo "Extracted <b>autoload.php</b> already exists. Skipping phar extraction.";
} else {
    if (!is_dir("composerInstalation")) {
        mkdir("composerInstalation");
    }

    $composerPhar = new Phar("Composer.phar");
    $composerPhar->extractTo("composerInstalation/");
}

/**
 * This requires the phar to have been extracted successfully.
 */
require 'composerInstalation/vendor/autoload.php';

/**
 * Use the Composer classes
 */
use Composer\Command\UpdatedteCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Composer\Console\Application;

/**
 * Run the app
 */
$application = new Application();
$application->setAutoExit(false);
$application->run(new ArrayInput(['command' => 'install']));

echo "<br>";
echo "Composer install done.";

sleep(15);

header("Location: /");
