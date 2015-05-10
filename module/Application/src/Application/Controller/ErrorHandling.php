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
 * @category   Application\Error
 * @package    ZendPress
 * @author     Stanimir Dimitrov <stanimirdim92@gmail.com>
 * @copyright  2015 Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 * @version    0.03
 * @link       TBA
 */

namespace Application\Controller;

use Zend\Http\PhpEnvironment\RemoteAddress;

class ErrorHandling
{
    /**
     * Default destination
     *
     * @var string $destination
     */
    private $destination = './data/logs/';

    /**
     * @var null $logger;
     */
    private $logger = null;

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
        if (!$destination) {
            $this->destination = $destination;
        }
    }

    /**
     * @param null $e Exception
     */
    public function logException($e = null)
    {
        $i = 1;
        do {
            $messages[] = $i++ . ": " . $e->getMessage();
        } while ($e->getPrevious());

        $log =  PHP_EOL."Exception: ".implode("", $messages);
        $log .=  PHP_EOL."Code: ".$e->getCode();
        $log .=  PHP_EOL."File: ".$e->getFile();
        $log .= PHP_EOL."Trace: ".$e->getTraceAsString();
        $this->logger->err($log);
    }

    /**
     * @param MvcEvent $e
     * @param ServiceManager $sm
     * @param Container $cache
     * @param string $userRole
     */
    public function logAuthorisationError($e, $sm, $cache, $userRole)
    {
        $remote = new RemoteAddress();

        if ($cache->role == 1) {
            $userRole = $cache->role;
        } elseif ($cache->role == 10) {
            $userRole = $cache->role;
        }

        $errorMsg = " *** APPLICATION LOG ***
        Controller: " . $e->getRouteMatch()->getParam('controller') . ",
        Controller action: " . $e->getRouteMatch()->getParam('action') . ",
        User role: " . $userRole. ",
        User id: " . (isset($cache->user->id) ? $cache->user->id : "Guest"). ",
        Admin: " . (isset($cache->user->admin) ? "Yes" : "No"). ",
        IP: " . $remote->getIpAddress() . ",
        Browser string: " . $sm->get("Request")->getServer()->get('HTTP_USER_AGENT') . ",
        Date: " . date("Y-m-d H:i:s", time()) . ",
        Full URL: ".$sm->get("Request")->getRequestUri().",
        User port: ".$_SERVER["REMOTE_PORT"].",
        Remote host addr: ".gethostbyaddr($remote->getIpAddress()).",
        Method used: " . $sm->get("Request")->getMethod() . "\n";

        $log = new \Zend\Log\Logger();
        $writer = new \Zend\Log\Writer\Stream($this->destination . date('F') . '.txt');
        $log->addWriter($writer);
        $log->info($errorMsg);
        return $log;
    }
}
