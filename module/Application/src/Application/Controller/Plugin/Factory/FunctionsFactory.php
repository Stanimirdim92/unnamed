<?php

/**
 * @copyright  2015 (c) Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 *
 * @version    0.0.21
 *
 * @link       TBA
 */

namespace Application\Controller\Plugin\Factory;

use Application\Controller\Plugin\Functions;

class FunctionsFactory
{
    /**
     * @{inheritDoc}
     */
    public function __invoke()
    {
        $plugin = new Functions();

        return $plugin;
    }
}
