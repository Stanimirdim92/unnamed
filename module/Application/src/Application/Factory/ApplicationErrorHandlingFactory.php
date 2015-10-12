<?php

/**
 * @copyright  2015 (c) Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 *
 * @version    0.0.17
 *
 * @link       TBA
 */

namespace Application\Factory;

use Zend\Log\Logger;
use Application\Controller\ErrorHandling;

final class ApplicationErrorHandlingFactory
{
    /**
     * {@inheritDoc}
     */
    public function __invoke()
    {
        $logger = new ErrorHandling(new Logger());

        return $logger;
    }
}
