<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

// Composer autoloading
if (is_dir('vendor/zendframework') && is_file('vendor/autoload.php')) {
    $loader = include 'vendor/autoload.php';
}

if (class_exists('Zend\Loader\AutoloaderFactory') && is_file('config/autoload/unnamed.local.php')) {
    return;
}

if (!class_exists('Zend\Loader\AutoloaderFactory') || !is_file('config/autoload/unnamed.local.php')) {
    if (!is_file('public/install.php')) {
         throw new \RuntimeException('Installation file is missing. Process cannot be started.');
    }
    header('Location: /install.php');
    return;
}
