<?php
/**
 * MIT License
 * ===========
 *
 * Copyright (c) 2015 Stanimir Dimitrov <stanimirdim92@gmail.com>
 *
 * Permission is hereby granted, free of charge, to any person obtaining
 * a copy of this software and associated documentation files (the
 * "Software"), to deal in the Software without restriction, including
 * without limitation the rights to use, copy, modify, merge, publish,
 * distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to
 * the following conditions:
 *
 * The above copyright notice and this permission notice shall be included
 * in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS
 * OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
 * IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY
 * CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT,
 * TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE
 * SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 *
 * @category   index
 * @package    ZendPress
 * @author     Stanimir Dimitrov <stanimirdim92@gmail.com>
 * @copyright  2015 Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 * @version    0.03
 * @link       TBA
 */

/**
 * Set default php.ini settings.
 *
 * Below lines includes security|error fixes
 */
ini_set('cgi.fix_pathinfo', 0);
ini_set('register_globals', 0);
error_reporting(0);
ini_set("display_errors", 0);
ini_set("display_startup_errors", 0);
ini_set("track_errors", 0);
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
if (ini_get('date.timezone') == '') {
    date_default_timezone_set('UTC');
}

/**
 * Set global ENV. Used for debugging
 */
if (isset($_SERVER['APPLICATION_ENV']) && $_SERVER["APPLICATION_ENV"] === 'development') {
    define("APP_ENV", 'development');
} else {
    define("APP_ENV", "production");
}

/**
 * Display all errors when APPLICATION_ENV is set to development.
 */
if (APP_ENV === 'development') {
    /**
     * Needed for ZendDeveloperTools
     */
    if (version_compare(PHP_VERSION, '5.4', '<')) {
        define('REQUEST_MICROTIME', microtime(true));
    }
    error_reporting(E_ALL);
    ini_set("display_errors", 1);
    ini_set("display_startup_errors", 1);
    ini_set("track_errors", 1);
}

/**
 * Check PHP and MySQL versions
 */
define("MIN_PHP_VER", "5.4");
define("ZEND_PRESS_VER", "0.03");

if (version_compare(MIN_PHP_VER, PHP_VERSION, '>' )) {
    header( 'Content-Type: text/html; charset=utf-8' );
    die(sprintf('Your server is running PHP version <b>%1$s</b> but ZendPress <b>%2$s</b> requires at least <b>%3$s</b> or higher</b>.', PHP_VERSION, ZEND_PRESS_VER, MIN_PHP_VER));
}

/**
 * Minimum required extensions
 */
if (!extension_loaded("PDO")        &&
    !extension_loaded("mysql")      &&
    !extension_loaded("mysqli")     &&
    !extension_loaded("mcrypt")     &&
    !extension_loaded("mbstring")   &&
    !extension_loaded("pdo_mysql")
    ) {
    header( 'Content-Type: text/html; charset=utf-8' );
    die(sprintf('One or more of these <b>%1$s</b> required extensions by ZendPress are missing, please enable them.', implode(", ", array("mysql", "mysqli", "PDO", "pdo_mysql", "mcrypt", "mbstring"))));
}

/*================================================================================
    PHP $_SERVER fixes are taken from Wordpress wp_includes/load.php. Thanks guys!
 =================================================================================*/

/**
 * Fix server differences
 */
$_SERVER = array_merge(array('SERVER_SOFTWARE' => '','REQUEST_URI' => ''), $_SERVER);

/**
 * Fix for IIS when running with PHP ISAPI
 */
if (empty($_SERVER['REQUEST_URI']) ||
   (php_sapi_name() != 'cgi-fcgi' && preg_match( '/^Microsoft-IIS\//', $_SERVER['SERVER_SOFTWARE']))
   ) {

    /**
     * IIS Mod-Rewrite
     */
    if (isset($_SERVER['HTTP_X_ORIGINAL_URL'])) {
        $_SERVER['REQUEST_URI'] = $_SERVER['HTTP_X_ORIGINAL_URL'];
    }
    /**
     * IIS Isapi_Rewrite
     */
    elseif (isset($_SERVER['HTTP_X_REWRITE_URL'])) {
        $_SERVER['REQUEST_URI'] = $_SERVER['HTTP_X_REWRITE_URL'];
    } else {
        /**
         * Use ORIG_PATH_INFO if there is no PATH_INFO
         */
        if (!isset($_SERVER['PATH_INFO']) && isset($_SERVER['ORIG_PATH_INFO'])) {
            $_SERVER['PATH_INFO'] = $_SERVER['ORIG_PATH_INFO'];
        }

        /**
         * Some IIS + PHP configurations puts the script-name in the path-info (No need to append it twice)
         */
        if (isset($_SERVER['PATH_INFO'])) {
            if ($_SERVER['PATH_INFO'] == $_SERVER['SCRIPT_NAME']) {
                $_SERVER['REQUEST_URI'] = $_SERVER['PATH_INFO'];
            } else {
                $_SERVER['REQUEST_URI'] = $_SERVER['SCRIPT_NAME'] . $_SERVER['PATH_INFO'];
            }
        }
        /**
         * Append the query string if it exists and isn't null
         */
        if (!empty($_SERVER['QUERY_STRING'])) {
            $_SERVER['REQUEST_URI'] .= '?' . $_SERVER['QUERY_STRING'];
        }
    }
}

/**
 * Fix for PHP as CGI hosts that set SCRIPT_FILENAME to something ending in php.cgi for all requests
 */
if (isset($_SERVER['SCRIPT_FILENAME']) && (strpos($_SERVER['SCRIPT_FILENAME'], 'php.cgi') == strlen($_SERVER['SCRIPT_FILENAME']) - 7 )) {
    $_SERVER['SCRIPT_FILENAME'] = $_SERVER['PATH_TRANSLATED'];
}

/**
 * Fix for Dreamhost and other PHP as CGI hosts
 */
if (strpos($_SERVER['SCRIPT_NAME'], 'php.cgi') !== false) {
    unset($_SERVER['PATH_INFO']);
}

/**
 * Fix empty PHP_SELF
 */
if (empty($_SERVER['PHP_SELF'])) {
    $_SERVER['PHP_SELF'] = $PHP_SELF = preg_replace( '/(\?.*)?$/', '', $_SERVER["REQUEST_URI"]);
}

/**
 * Hack CGI https://github.com/sitrunlab/LearnZF2/pull/128#issuecomment-98054110
 */
if (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
    $_SERVER['HTTP_AUTHORIZATION'] = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
}

/**
 * This makes our life easier when dealing with paths. Everything is relative
 * to the application root now.
 */
chdir(dirname(__DIR__));

/**
 * Decline static file requests back to the PHP built-in webserver
 */
if (php_sapi_name() === 'cli-server' && is_file(__DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH))) {
    return false;
}

/**
 * Setup autoloading
 */
require 'init_autoloader.php';

/**
 * Run the application!
 */
Zend\Mvc\Application::init(require 'config/application.config.php')->run();
