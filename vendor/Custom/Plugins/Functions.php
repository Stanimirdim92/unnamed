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
 * @version    0.0.5
 * @link       TBA
 */

namespace Custom\Plugins;

use Zend\Db\Adapter\Adapter;
use Zend\Session\Container;
use Zend\Math\Rand;
use Zend\Db\Adapter\Driver\Pdo\Statement;

class Functions
{
    /**
     * @var  Zend\Db\Adapter\Adapter
     */
    private static $adapter = null;

    /**
     * Create Adapter instance and cache it
     * @return  Adapter
     */
    final private static function getAdapter() {
        if (!static::$adapter) {
            static::$adapter = include('config/autoload/local.php');
        }
        return new Adapter(static::$adapter['db']);
    }

    /**
     * Create plain mysql queries.
     *
     * @param string $query
     * @param array $params for the moment $params is optional
     * @param bool $returnResults
     * @return array
     */
    public static function createPlainQuery($query = null, array $params = [])
    {
        if (empty($query)) {
            throw new \InvalidArgumentException('Query must not be empty');
        }

        if (empty($params)) {
            throw new \InvalidArgumentException('Query parameturs must not be empty');
        }

        $db = static::getAdapter();
        $stmt = $db->query($query, $params);
        $stmt->buffer();
        return $stmt->toArray();
    }

    /**
     * @link https://github.com/ircmaxell/password_compat/
     * @link http://blog.ircmaxell.com/2015/03/security-issue-combining-bcrypt-with.html
     * @see /vendor/Custom/Plugins/Password.php
     * @param null|string $password the user password in plain text
     * @return  the encrypted password with tha salt. Salt comes from password_hash
     * @todo add default php password_hash implementation
     */
    public static function createPassword($password = null)
    {
        require_once('/vendor/Custom/Plugins/Password.php');

        if (empty($password)) {
            throw new \Exception("Password cannot be empty");
        }

        if (self::strLength($password) < 8) {
            throw new \Exception("Password must be atleast 8 characters long");
        }

        $pw = password_hash($password, PASSWORD_BCRYPT, array("cost" => 13));

        if (!$pw) {
            throw new \Exception("Error while generating password");
        }

        return $pw;
    }

    /**
     * @param string $string The input string
     * @return int The number of bytes
     */
    public static function strLength($string = null)
    {
        return mb_strlen($string, '8bit');
    }

    /**
     * Generate a random 64 chars long string via the OpenSSL|MCRYPT|M_RAND functions.
     *
     * @return string
     */
    public static function generateToken()
    {
        return base64_encode(Rand::getBytes(48, true));
    }

    /**
     * Detect SSL/TLS protocol. If true activate cookie_secure key
     *
     * @return bool
     */
    public static function isSSL()
    {
        if (isset($_SERVER['HTTPS'])) {
            if ('on' == strtolower($_SERVER['HTTPS']) || '1' == $_SERVER['HTTPS']) {
                return true;
            }
        } elseif (isset($_SERVER['SERVER_PORT']) && ('443' == $_SERVER['SERVER_PORT'])) {
            return true;
        }
        return false;
    }
}
