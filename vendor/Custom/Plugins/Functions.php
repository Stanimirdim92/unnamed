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
 * @category   Custom\Plugins
 * @package    ZendPress
 * @author     Stanimir Dimitrov <stanimirdim92@gmail.com>
 * @copyright  2015 Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 * @version    0.03
 * @link       TBA
 */

namespace Custom\Plugins;

use Zend\Session\Container;
use Zend\Math\Rand;

class Functions
{
    /**
     * @var string $bcryptCost
     */
    private static $bcryptCost = 13;

    /**
     * @var null $translation
     */
    private static $translation = null;

    /**
     * This method loads into translation all term translations from the database
     *
     * @param Int $language
     * @param Bool $reload - will force to reload even if translation exists
     * @return Zend\Session\Container
     */
    public static function initTranslations($language = 1, $reload = false)
    {
        static::$translation = new Container('translations');
        if($reload)
        {
            $result = self::createPlainQuery("SELECT `termtranslation`.`translation`, `term`.`name` FROM `term` INNER JOIN `termtranslation` ON `term`.`id`=`termtranslation`.`term` WHERE `termtranslation`.`language`='".(int)$language."' ORDER BY `term`.`name` ASC");
            if (count($result) > 0)
            {
                foreach($result as $r)
                {
                    if(!empty($r['name']))
                    {
                        static::$translation->__set($r['name'], $r['translation']);
                    }
                }
            }
        }
        return static::$translation;
    }

    /**
     * Create plain mysql queries.
     *
     * @param String $query the plain query
     * @param Bool $returnResults specify if the function should return results
     * @return array
     */
    public static function createPlainQuery($query = null, $returnResults = true)
    {
        if (empty($query))
        {
            $returnResults = false;
            throw new \Exception(__METHOD__ . ' must not be empty');
        }

        $local = include($_SERVER['DOCUMENT_ROOT'].'/../config/autoload/local.php');
        $db = new \Zend\Db\Adapter\Adapter($local['db']);
        $stmt = $db->createStatement();
        $stmt->prepare($query);
        $result = $stmt->execute();
        $arr = array();
        if($returnResults === true)
        {
            while($result->next())
            {
                $arr[] = $result->current();
            }
        }
        return $arr;
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
        require '/vendor/Custom/Plugins/Password.php';

        if (empty($password))
        {
            throw new \Exception("Password cannot be empty");
        }
        if (self::strLength($password) < 8)
        {
            throw new \Exception("Password must be atleast 8 characters long");
        }
        $pw = password_hash($password, PASSWORD_BCRYPT, array("cost" => static::$bcryptCost));
        if(!$pw)
        {
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
        if (function_exists('mb_strlen'))
        {
            return mb_strlen($string, '8bit');
        }
        return strlen($string);
    }

    /**
     * Generate a random 64 chars long string via the OpenSSL|MCRYPT|M_RAND functions.
     *
     * @param int $number is used to determinate how long should the token. Currently we pass 48 and the output should be 64
     * @return string
     */
    public static function generateToken($number = null)
    {
        if ($number === 48)
        {
            return base64_encode(Rand::getBytes($number, true));
        }
        throw new \Exception("Error while generating a token");
    }
}
?>
