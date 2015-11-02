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
 * Check requirements.
 */
if (version_compare("5.5.28", PHP_VERSION, '>')) {
    throw new \Exception(sprintf('Your server is running PHP version <b>%1$s</b>, but the system <b>%2$s</b> requires at least <b>%3$s</b> or higher</b>.', PHP_VERSION, "0.0.21", "5.5.28"));
}

/**
 * Minimum required extensions.
 */
if (!extension_loaded("Zend OPcache") || !extension_loaded("mcrypt") || !extension_loaded("mbstring") || !extension_loaded("intl") || !extension_loaded("gd")) {
    throw new \Exception(sprintf('One or more of these <b>%1$s</b> required extensions are missing, please enable them.', implode(", ", ["Zend OPcache", "mcrypt", "mbstring", "intl", "gd"])));
}

/**
 * Set global ENV. Used for debugging.
 */
define("APP_ENV", getenv("APPLICATION_ENV"));

/**
 * Handle errors
 */
error_reporting((APP_ENV === 'development' ? E_ALL : 0));
ini_set("display_errors", (APP_ENV === 'development'));
ini_set("display_startup_errors", (APP_ENV === 'development'));

/**
 * Fixes files and server encoding.
 */
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');


/**
 * Some server configurations are missing a date timezone and PHP will throw a warning.
 */
if (ini_get('date.timezone') == '') {
    date_default_timezone_set('UTC');
}

/**
 * Hack CGI https://github.com/sitrunlab/LearnZF2/pull/128#issuecomment-98054110.
 */
if (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
    $_SERVER['HTTP_AUTHORIZATION'] = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
}

/**
 * This makes our life easier when dealing with paths. Everything is relative.
 * to the application root now.
 */
chdir(dirname(__DIR__));

/**
 * Setup autoloading.
 */
if (!is_file('vendor/autoload.php')) {
    header('Location: /install.php');
}
require_once 'vendor/autoload.php';

/**
 * Run the application!
 */
Zend\Mvc\Application::init(include 'config/application.config.php')->run();
