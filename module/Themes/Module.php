<?php

/**
 * @copyright  2015 (c) Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 * @version    0.0.18
 * @link       TBA
 */

namespace Themes;

use Zend\ModuleManager\Feature\ConfigProviderInterface;

final class Module implements ConfigProviderInterface
{
    /**
     * @return array|\Traversable
     */
    public function getConfig()
    {
        $dir = new \DirectoryIterator(__DIR__);

        foreach ($dir as $file) {
            if ($file->isDir() && !$file->isDot()) {
                $hasConfig = __DIR__.DIRECTORY_SEPARATOR.$file->getBasename()."/config/module.config.php";

                if (is_file($hasConfig)) {
                    $config["themes"][$file->getBasename()] = include $hasConfig;
                }
            }
        }

        return $config;
    }
}
