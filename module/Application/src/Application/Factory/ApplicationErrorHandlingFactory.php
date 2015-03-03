<?php 
namespace Application\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Log\Logger;
use Zend\Log\Writer\Stream as LogWriterStream;
use Application\Controller\ErrorHandling as ErrorHandlingService;

class ApplicationErrorHandlingFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $sm = null)
    {
        $log = new Logger();
        $writer = new LogWriterStream('./data/logs/front_end_log_' . date('F') . '.txt');
        $log->addWriter($writer);
        return new ErrorHandlingService($log);
    }

}

?>