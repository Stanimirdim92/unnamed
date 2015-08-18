<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

/**
 * This autoloading setup is really more complicated than it needs to be for most
 * applications. The added complexity is simply to reduce the time it takes for
 * new developers to be productive with a fresh skeleton. It allows autoloading
 * to be correctly configured, regardless of the installation method and keeps
 * the use of composer completely optional. This setup should work fine for
 * most users, however, feel free to configure autoloading however you'd like.
 */
// Composer autoloading
if (file_exists('vendor/autoload.php')) {
    $loader = include 'vendor/autoload.php';
}

$zf2Path = false;

if (is_dir('vendor/zendframework/zendframework/library')) {
    $zf2Path = 'vendor/zendframework/zendframework/library';
} elseif (is_dir('vendor/ZF2/library')) {
    $zf2Path = 'vendor/ZF2/library';
} elseif (getenv('ZF2_PATH')) {      // Support for ZF2_PATH environment variable or git submodule
    $zf2Path = getenv('ZF2_PATH');
} elseif (get_cfg_var('zf2_path')) { // Support for zf2_path directive value
    $zf2Path = get_cfg_var('zf2_path');
}

if ($zf2Path !== false) {
    if (isset($loader)) {
        $loader->add('Zend', $zf2Path);
    } else {
        include $zf2Path . '/Zend/Loader/AutoloaderFactory.php';
        Zend\Loader\AutoloaderFactory::factory([
            'Zend\Loader\StandardAutoloader' => [
                'autoregister_zf' => true
            ]
        ]);
    }
}

// Yes, for real :)
$style = "
    background-color: #56AA1C;
    border-color: #56AA1C;
    display: inline-block;
    padding: 8px 14px;
    margin-bottom: 0;
    font-size: 14px;
    font-weight: normal;
    line-height: 1.4;
    text-align: center;
    vertical-align: middle;
    cursor: pointer;
    border: 1px solid transparent;
    border-radius: 3px;
    white-space: nowrap;
    color: #FFFFFF;
    text-decoration: none;
    font-family: 'DejaVu Sans', 'Trebuchet MS', Verdana, 'Verdana Ref', sans-serif;
";

if (!class_exists('Zend\Loader\AutoloaderFactory')) {
    die(sprintf('<p style="text-align: center;color: #737373; font-size: 14px;font-family: \'DejaVu Sans\', \'Trebuchet MS\', Verdana, \'Verdana Ref\', sans-serif;">Unable to load Zend Framework. <br> <br> <a style="'.$style.'" href="/install.php">Click to install it.</a></p>'));
}
