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
 * @version    0.0.12
 * @link       TBA
 */

namespace Application\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mail\Transport\SmtpOptions;
use Zend\Mail\Message;
use Zend\Mime\Part as MimePart;
use Zend\Mime\Message as MimeMessage;
use Zend\Mvc\Controller\Plugin\FlashMessenger;

final class Mailing extends AbstractPlugin
{
    /**
     * @var FlashMessenger $flashMessenger
     */
    private $flashMessenger = null;

    /**
     * @param FlashMessenger $flashMessenger
     */
    public function __construct(FlashMessenger $flashMessenger = null)
    {
        $this->flashMessenger = $flashMessenger;
    }

    /**
     * @param string $to
     * @param string $toName
     * @param string $subject
     * @param string $message
     * @param string $from
     * @param string $fromName
     * @return boolean
     * @todo  extend with more config methods
     */
    public function sendMail($to, $toName, $subject, $message, $from, $fromName)
    {
        $transport = new SmtpTransport();
        $options   = new SmtpOptions([
            'host'              => 'smtp.gmail.com',
            'name'              => 'Unnamed',
            'connection_class'  => 'login',
            'connection_config' => [
                'username' => '',
                'password' => '',
                'ssl' => 'tls',
            ],
            'port' => '465',
        ]);
        $htmlPart = new MimePart($message);
        $htmlPart->type = "text/html";

        $body = new MimeMessage();
        $body->setParts([$htmlPart]);

        $mail = new Message();
        $mail->setFrom($from, $fromName);
        $mail->addTo($to, $toName);
        $mail->setSubject($subject);
        $mail->setEncoding("UTF-8");
        $mail->setBody($body);
        $mail->getHeaders()->addHeaderLine("MIME-Version: 1.0");
        $mail->getHeaders()->addHeaderLine('Content-Type', 'text/html; charset=UTF-8');

        try {
            $transport->setOptions($options);
            $transport->send($mail);
            return true;
        } catch (\Exception $e) {
            return $this->flashMessenger->addMessage("Email not send", "error");
        }
    }
}
