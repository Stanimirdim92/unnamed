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

use Zend\Math\Rand;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\Db\Adapter\Adapter;
use Application\Exception\InvalidArgumentException;

final class Functions extends AbstractPlugin
{
    /**
     * @var Adapter $adapter
     */
    private $adapter = null;

    /**
     * @param Adapter $adapter
     */
    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * Execute plain mysql queries.
     *
     * @param string $query
     * @throws InvalidArgumentException
     * 
     * @return ResultSet|null
     */
    public function createPlainQuery($query)
    {
        if (empty($query)) {
            throw new InvalidArgumentException('Query must not be empty');
        }

        $stmt = $this->adapter->query((string) $query);
        $result = $stmt->execute();
        $result->buffer();

        if ($result->count() > 0 && $result->isQueryResult() && $result->isBuffered()) {
            return $result;
        }

        return null;
    }

    /**
     * @link https://github.com/ircmaxell/password_compat/
     * @link http://blog.ircmaxell.com/2015/03/security-issue-combining-bcrypt-with.html
     * @todo add default php password_hash implementation
     * 
     * @param string $password the user password in plain text
     * @throws InvalidArgumentException
     * 
     * @return  the encrypted password with the salt. Salt comes from password_hash
     */
    public static function createPassword($password)
    {
        require_once('/module/Application/src/Application/Entity/Password.php');

        if (empty($password)) {
            throw new InvalidArgumentException("Password cannot be empty");
        }

        if (static::strLength($password) < 8) {
            throw new InvalidArgumentException("Password must be atleast 8 characters long");
        }

        $pw = password_hash($password, PASSWORD_BCRYPT, array("cost" => 13));

        if (!$pw) {
            throw new InvalidArgumentException("Error while generating password");
        }

        return $pw;
    }

    /**
     * @param string $string The input string
     * @return int The number of bytes
     */
    public static function strLength($string)
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
