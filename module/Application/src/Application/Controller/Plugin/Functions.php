<?php

/**
 * @copyright  2015 (c) Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 *
 * @version    0.0.21
 *
 * @link       TBA
 */

namespace Application\Controller\Plugin;

use Zend\Math\Rand;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Application\Exception\InvalidArgumentException;
use Application\Exception\RuntimeException;

final class Functions extends AbstractPlugin
{
    /**
     * Never set the salt parameter for this function unless you are not a security expert who knows what he is doing.
     *
     * @link http://blog.ircmaxell.com/2015/03/security-issue-combining-bcrypt-with.html
     *
     * @param string $password the user password in plain text
     *
     * @throws InvalidArgumentException
     * @throws RuntimeException
     *
     * @return string the encrypted password with the salt. Salt comes from password_hash
     */
    public static function createPassword($password)
    {
        if (empty($password)) {
            throw new InvalidArgumentException("Password cannot be empty");
        }

        if (static::strLength($password) < 8) {
            throw new InvalidArgumentException("Password must be atleast 8 characters long");
        }

        $pw = password_hash($password, PASSWORD_BCRYPT, ["cost" => 13]);

        if (empty($pw)) {
            throw new RuntimeException("Error while generating password");
        }

        return $pw;
    }

    /**
     * @param string $string The input string
     *
     * @return int The number of bytes
     */
    public static function strLength($string = null)
    {
        return mb_strlen($string, '8bit');
    }

    /**
     * Generate a random string via the OpenSSL|MCRYPT|M_RAND functions. and return a base64 encode of it.
     *
     * @return string
     */
    public static function generateToken()
    {
        return base64_encode(Rand::getBytes(mt_rand(1, 999999), true));
    }

    /**
     * Detect SSL/TLS protocol. If true activate cookie_secure key.
     *
     * @return bool
     */
    public static function isSSL()
    {
        $stats = false;
        if (isset($_SERVER['HTTPS'])) {
            if ('on' == strtolower($_SERVER['HTTPS']) || '1' == $_SERVER['HTTPS']) {
                $stats = true;
            }
        } elseif (isset($_SERVER['SERVER_PORT']) && ('443' == $_SERVER['SERVER_PORT'])) {
            $stats = true;
        }
        return $stats;
    }
}
