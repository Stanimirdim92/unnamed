<?php

/**
 * @copyright  2015 (c) Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 *
 * @version    0.0.16
 *
 * @link       TBA
 */

namespace Application\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Log\Logger;
use Zend\Log\Writer\Stream;
use Application\Controller\ErrorHandling;

final class ApplicationErrorHandlingFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function createService(ServiceLocatorInterface $servicLocator = null)
    {
        $log = new Logger();
        $log->addWriter(new Stream('./data/logs/front_end_log_'.date('F').'.txt'));
        $error =  new ErrorHandling($log);
        return $error;
    }
}
