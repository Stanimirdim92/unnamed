<?php 
namespace Admin\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Log\Logger;
use Zend\Log\Writer\Stream as LogWriterStream;
use Admin\Controller\ErrorHandling as ErrorHandlingService;

class AdminErrorHandlingFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $sm = null)
    {
        $log = new Logger();
        $writer = new LogWriterStream('./data/logs/log_' . date('F') . '.txt');
        $log->addWriter($writer);
        return new ErrorHandlingService($log);
    }
}

?>