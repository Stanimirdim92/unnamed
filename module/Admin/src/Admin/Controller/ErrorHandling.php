<?php
/**
 * MIT License
 * ===========
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
 * @version    0.0.10
 * @link       TBA
 */

namespace Admin\Controller;

use Zend\Http\PhpEnvironment\RemoteAddress;
use Admin\Exception\AuthorizationException;
use Zend\Log\Writer\Stream;
use Zend\Mvc\MvcEvent;

class ErrorHandling
{
    /**
     * Default destination
     *
     * @var string $destination
     */
    private $destination = './data/logs/';

    /**
     * @var null|Logger $logger;
     */
    private $logger = null;

    /**
     * @param Zend\Log\Logger $logger
     */
    public function __construct($logger = null)
    {
        $this->logger = $logger;
    }

    /**
     * Set log destination
     *
     * @param null $destination set the destination where you want to save the log
     * @return string
     */
    public function setDestination($destination = null)
    {
        $this->destination = $destination;
    }

    /**
     * @param null $e Exception
     */
    private function logException($e = null)
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
    }

    /**
     * @param  Zend\Mvc\MvcEvent $e
     * @param  ServiceManager $sm
     *
     * @return MvcEvent
     */
    public function logError(MvcEvent $e = null, $sm = null)
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
     * @param ServiceManager $sm
     * @param Container $cache
     * @param string $userRole
     * @todo add user data like id and name
     */
    private function logAuthorisationError(MvcEvent $e = null, $sm = null)
    {
        $remote = new RemoteAddress();

        $errorMsg = " *** ADMIN LOG ***
        Controller: ".$e->getRouteMatch()->getParam('controller').",
        Controller action: ".$e->getRouteMatch()->getParam('action').",
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
        return $this->logger;
    }
}
