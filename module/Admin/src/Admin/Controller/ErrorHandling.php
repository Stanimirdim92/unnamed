<?php
namespace Admin\Controller;
use Zend\Log\Logger;
use Zend\Log\Writer\Stream as LogWriterStream;
class ErrorHandling
{
    protected $logger;

    function __construct($logger)
    {
        $this->logger = $logger;
    }

    function logException($e)
    {
        $trace = $e->getTraceAsString();
        $i = 1;
        do {
            $messages[] = $i++ . ": " . $e->getMessage();
        } while ($e = $e->getPrevious());

        $log = "Exception:n" . implode("n", $messages);
        $log .= "nTrace:n" . $trace;
        $this->logger->err($log);
    }

    function logAuthorisationError($errorMsg)
    {
        $filename = 'denyAccess_' . date('F') . '.txt';
        $log = new Logger();
        $writer = new LogWriterStream('./data/logs/' . $filename);
        $log->addWriter($writer);
        $log->info($errorMsg);
        return $log;
    }
}