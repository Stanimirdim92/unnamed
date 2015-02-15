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
 * Activate proper Zend DevTools for lower PHP versions
 */
if (isset($_SERVER['DEBUG_ENV']) && $_SERVER['DEBUG_ENV'] == true) {
    if (version_compare(PHP_VERSION, '5.4', '<')) {
      define('REQUEST_MICROTIME', microtime(true));
    }
}

 /**
  * Display all errors when APPLICATION_ENV is development.
  */
 if (isset($_SERVER['APPLICATION_ENV']) && $_SERVER['APPLICATION_ENV'] === 'development') {
     error_reporting(E_ALL);
     ini_set("display_errors", 1);
     ini_set("display_startup_errors", 1);
     ini_set("track_errors", 1);
 }

 /**
  * This makes our life easier when dealing with paths. Everything is relative
  * to the application root now.
  */
 chdir(dirname(__DIR__));

 // Decline static file requests back to the PHP built-in webserver
 if (php_sapi_name() === 'cli-server' && is_file(__DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH))) {
     return false;
 }

 // Setup autoloading
 require 'init_autoloader.php';

 // Run the application!
 Zend\Mvc\Application::init(require 'config/application.config.php')->run();