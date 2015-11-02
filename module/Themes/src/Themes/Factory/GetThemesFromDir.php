<?php

/**
 * @copyright  2015 (c) Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 *
 * @version    0.0.21
 *
 * @link       TBA
 */

namespace Themes\Factory;

use DirectoryIterator;

final class GetThemesFromDir
{
    /**
     * {@inheritdoc}
     */
    public function __invoke()
    {
        $path = __DIR__.'/../../../themes/';
        $dir = new DirectoryIterator($path);
        $themesConfig = [];

        foreach ($dir as $file) {
            if (!$file->isDot()) {
                $hasConfig = $path.$file->getBasename().'/config/module.config.php';

                if (is_file($hasConfig)) {
                    $themesConfig['themes'][$file->getBasename()] = include $hasConfig;
                }
            }
        }

        return $themesConfig;
    }
}
