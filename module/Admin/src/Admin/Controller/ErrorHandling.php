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
 * @category   Admin\Error
 * @package    ZendPress
 * @author     Stanimir Dimitrov <stanimirdim92@gmail.com>
 * @copyright  2015 Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 * @version    0.03
 * @link       TBA
 */

namespace Admin\Controller;

class ErrorHandling
{
    /**
     * Default destination
     *
     * @var string $_destination
     */
    private $_destination = './data/logs/';

    /**
     * @var null $_logger;
     */
    private $_logger = null;

    public function __construct($logger = null)
    {
        $this->_logger = $logger;
    }

    /**
     * Set log destination
     *
     * @param null $destination set the destination where you want to save the log
     * @return string
     */
    public function setDestination($destination = null)
    {
        if ($destination != null) {
            $this->_destination = $destination;
        }
    }

    /**
     * @var null $e Exception
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
        $this->_logger->err($log);
    }

    /**
     * @var null $errorMsg
     */
    public function logAuthorisationError($errorMsg = null)
    {
        $log = new \Zend\Log\Logger();
        $writer = new \Zend\Log\Writer\Stream($this->_destination . date('F') . '.txt');
        $log->addWriter($writer);
        $log->info($errorMsg);
        return $log;
    }
}
