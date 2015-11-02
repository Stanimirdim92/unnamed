<?php

/**
 * @copyright  2015 (c) Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 *
 * @version    0.0.21
 *
 * @link       TBA
 */

namespace Application\Controller;

use Zend\Http\PhpEnvironment\RemoteAddress;
use Application\Exception\AuthorizationException;
use Zend\Log\Logger;
use Zend\Log\Writer\Stream;
use Zend\Mvc\MvcEvent;
use Zend\ServiceManager\ServiceLocatorInterface;
use Application\Exception\InvalidArgumentException;

final class ErrorHandling
{
    /**
     * Default destination.
     *
     * @var string $destination
     */
    private $destination = './data/logs/';

    /**
     * @var Logger $logger;
     */
    private $logger;

    /**
     * @param Logger $logger
     */
    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Set log destination.
     *
     * @param string $destination set the destination where you want to save the log.
     *
     * @return ErrorHandling
     */
    public function setDestination($destination)
    {
        $destination = (string) $destination;

        if (!is_dir($destination)) {
            throw new InvalidArgumentException(
                "Public directory '{$destination}' not found or not a directory"
            );
        } elseif (!is_writable($destination)) {
            throw new InvalidArgumentException(
                "Public directory '{$destination}' not writable"
            );
        } elseif (!is_readable($destination)) {
            throw new InvalidArgumentException(
                "Public directory '{$destination}' not readable"
            );
        }

        $this->destination = rtrim(realpath($destination), DIRECTORY_SEPARATOR);
        return $this;
    }

    /**
     * @param \Exception $exception
     *
     * @return ErrorHandling
     */
    private function logException(\Exception $exception)
    {
        $log = PHP_EOL."Exception: ".$exception->getMessage();
        $log .= PHP_EOL."Code: ".$exception->getCode();
        $log .= PHP_EOL."File: ".$exception->getFile();
        $log .= PHP_EOL."Trace: ".$exception->getTraceAsString();
        $this->logger->addWriter(new Stream($this->destination.'front_end_log_'.date('F').'.txt'));
        $this->logger->err($log);

        return $this;
    }

    /**
     * @param MvcEvent $event
     * @param ServiceLocatorInterface $sm
     */
    public function logError(MvcEvent $event, ServiceLocatorInterface $sm)
    {
        $exception = $event->getParam("exception");
        if ($exception instanceof AuthorizationException) {
            $this->logAuthorisationError($event, $sm);
            $this->logException($exception);
        } elseif ($exception !== null) {
            $this->logException($exception);
        }

        $event->getResponse()->setStatusCode(404);
        $event->getViewModel()->setVariables(
            [
            'message' => '404 Not found',
            'reason' => 'The link you have requested doesn\'t exists',
            'exception' => ($exception !== null ? $exception->getMessage() : ""),
            ]
        );
        $event->getViewModel()->setTemplate('error/index');
        $event->stopPropagation();
    }

    /**
     * @param MvcEvent $event
     * @param ServiceLocatorInterface $sm
     *
     * @return ErrorHandling
     */
    private function logAuthorisationError(MvcEvent $event, ServiceLocatorInterface $sm)
    {
        $remote = new RemoteAddress();

        $errorMsg = " *** LOG ***
        Controller: ".$event->getRouteMatch()->getParam('controller').",
        Controller action: ".$event->getRouteMatch()->getParam('action').",
        IP: ".$remote->getIpAddress().",
        Browser string: ".$sm->get("Request")->getServer()->get('HTTP_USER_AGENT').",
        Date: ".date("Y-m-d H:i:s", time()).",
        Full URL: ".$sm->get("Request")->getRequestUri().",
        User port: ".$_SERVER["REMOTE_PORT"].",
        Remote host addr: ".gethostbyaddr($remote->getIpAddress()).",
        Method used: ".$sm->get("Request")->getMethod()."\n";

        $writer = new Stream($this->destination.date('F').'.txt');
        $this->logger->addWriter($writer);
        $this->logger->info($errorMsg);

        return $this;
    }
}
