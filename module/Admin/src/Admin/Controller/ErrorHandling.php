<?php
namespace Admin\Controller;

class ErrorHandling
{
    private $_logger;

    public function __construct($logger)
    {
        $this->_logger = $logger;
    }

    public function logException($e)
    {
        $i = 1;
        do {
            $messages[] = $i++ . ": " . $e->getMessage();
        } while ($e->getPrevious());

        $log =  PHP_EOL."Exception: ".implode("", $messages);
        $log .=  PHP_EOL."Code: ".$e->getCode();
        $log .=  PHP_EOL."File: ".$e->getFile();
        $log .= PHP_EOL."Trace: ".$e->getTraceAsString();
        $this->_logger->err($log);
    }

    public function logAuthorisationError($errorMsg = null)
    {
        $log = new \Zend\Log\Logger();
        $writer = new \Zend\Log\Writer\Stream('./data/logs/denyAccess_' . date('F') . '.txt');
        $log->addWriter($writer);
        $log->info($errorMsg);
        return $log;
    }
}