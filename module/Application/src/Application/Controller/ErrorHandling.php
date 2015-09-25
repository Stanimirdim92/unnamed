<?php
/**
 * MIT License.
 *
 * Copyright (c) 2015 Stanimir Dimitrov <stanimirdim92@gmail.com>
 *
 * Permission is hereby granted, free of charge, to any person obtaining
 * a copy of this software and associated documentation files (the
 * "Software"), to deal in the Software without restriction, including
 * without limitation the rights to use, copy, modify, merge, publish,
 * distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to
 * the following conditions:
 *
 * The above copyright notice and this permission notice shall be included
 * in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS
 * OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
 * IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY
 * CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT,
 * TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE
 * SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 *
 * @author     Stanimir Dimitrov <stanimirdim92@gmail.com>
 * @copyright  2015 (c) Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 *
 * @version    0.0.13
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
    private $logger = null;

    /**
     * @param Logger $logger
     */
    public function __construct(Logger $logger = null)
    {
        $this->logger = $logger;
    }

    /**
     * Set log destination
     *
     * @param string $destination set the destination where you want to save the log.
     */
    public function setDestination($destination = null)
    {
        if (is_dir($destination) && is_writable($destination)) {
            $this->destination = (string) $destination;
        }
    }

    /**
     * @param \Exception $e
     */
    private function logException(\Exception $e = null)
    {
        $i = 1;
        $messages = [];
        while ($e->getPrevious()) {
            $messages[] = $i++ . ": " . $e->getMessage();
        }

        $log =  PHP_EOL."Exception: ".implode("", $messages);
        $log .=  PHP_EOL."Code: ".$e->getCode();
        $log .=  PHP_EOL."File: ".$e->getFile();
        $log .= PHP_EOL."Trace: ".$e->getTraceAsString();
        $this->logger->err($log);
        return $this->logger;
    }

    /**
     * @param MvcEvent $e
     * @param ServiceLocatorInterface $sm
     *
     * @return MvcEvent
     */
    public function logError(MvcEvent $e = null, ServiceLocatorInterface $sm = null)
    {
        $exception = $e->getParam("exception");
        if ($exception instanceof AuthorizationException) {
            $this->logAuthorisationError($e, $sm);
            $this->logException($exception);
        } elseif ($exception != null) {
            $this->logException($exception);
        }

        $e->getResponse()->setStatusCode(404);
        $e->getViewModel()->setVariables([
            'message' => '404 Not found',
            'reason' => 'The link you have requested doesn\'t exists',
            'exception' => ($exception != null ? $exception->getMessage() : ""),
        ]);
        $e->getViewModel()->setTemplate('error/index');
        $e->stopPropagation();
        return $e;
    }

    /**
     * @param MvcEvent $e
     * @param ServiceLocatorInterface $sm
     * @todo add user data such as id and name
     */
    private function logAuthorisationError(MvcEvent $e = null, ServiceLocatorInterface $sm = null)
    {
        $remote = new RemoteAddress();

        $errorMsg = " *** APPLICATION LOG ***
        Controller: ".$e->getRouteMatch()->getParam('controller').",
        Controller action: ".$e->getRouteMatch()->getParam('action').",
        IP: ".$remote->getIpAddress().",
        Browser string: ".$sm->get("Request")->getServer()->get('HTTP_USER_AGENT').",
        Date: ".date("Y-m-d H:i:s", time()).",
        Full URL: ".$sm->get("Request")->getRequestUri().",
        User port: ".$_SERVER["REMOTE_PORT"].",
        Remote host addr: ".gethostbyaddr($remote->getIpAddress()).",
        Method used: ".$sm->get("Request")->getMethod()."\n";

        $log = new Logger();
        $writer = new Stream($this->destination.date('F').'.txt');
        $log->addWriter($writer);
        $log->info($errorMsg);
        return $log;
    }
}
