<?php
namespace Custom\Plugins;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mail\Transport\SmtpOptions;

use Zend\Mail;
use Zend\Mime\Part as MimePart;
use Zend\Mime\Message as MimeMessage;

class Mailing extends AbstractActionController
{
    /**
     * @param String  $to
     * @param String  $toName
     * @param String  $subject
     * @param String  $message
     * @param String  $from
     * @param String  $fromName
     * @return boolean
     */
    public static function sendMail($to, $toName, $subject, $message, $from, $fromName)
    {
        $transport = new SmtpTransport();
        $options   = new SmtpOptions(array(
            'host'              => 'smtp.gmail.com',
            'connection_class'  => 'login',
            'connection_config' => array(
                'username' => 'psyxopat@gmail.com',
                'password' => 'rompompom'
            ),
            'port' => '587',
        ));
        $htmlPart = new MimePart($message);
        $htmlPart->type = "text/html";

        $body = new MimeMessage();
        $body->setParts(array($htmlPart));

        $mail = new Mail\Message();
        $mail->setFrom($from, $fromName);
        $mail->addTo($to, $toName);
        $mail->setSubject($subject);
        $mail->setEncoding("UTF-8");
        $mail->setBody($body);
        $mail->getHeaders()->addHeaderLine("MIME-Version: 1.0");
        $mail->getHeaders()->addHeaderLine('Content-Type', 'text/html; charset=UTF-8');
        try
        {
            $transport->setOptions($options);
            $transport->send($mail);
        }
        catch (\Exception $e)
        {
            echo "<pre>".print_r($e->getTraceAsString(), true)."</pre>";
            exit;
        }
        return true;
    }
}
?>