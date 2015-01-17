<?php
namespace Custom\Plugins;

use Zend\Session\Container;
use Zend\Db\Adapter\Driver\Pdo\Statement;
use Zend\Db\Adapter\Adapter;
use Zend\Math\Rand;

class Functions
{
    /**
     * @var string $bcryptCost
     */
    private static $bcryptCost = 13;

    /** 
     * This method loads into translation all term translations from the database
     *
     * @param Int $language
     * @param Bool $reload - will force to reload even if translation exists
     * @return Zend\Session\Container
     */
    public static function initTranslations($language = 1, $reload = false)
    {
        $total = self::createPlainQuery("SELECT COUNT(id) FROM `language` WHERE `active`='1'");

        if($language < 1 || $language > $total)
        {
            $language = 1;
        }
        
        $translation = new Container('translations');
        if(!isset($translation->initialized) || $reload)
        {
            $query = "SELECT `termtranslation`.`translation`, `term`.`name`
                      FROM `term` 
                      INNER JOIN `termtranslation`
                      ON `term`.`id`=`termtranslation`.`term`
                      WHERE `termtranslation`.`language`='".(int)$language."'
                      ORDER BY `term`.`name` ASC";

            $result = self::createPlainQuery($query);
            if (count($result) > 0)
            {
                foreach($result as $r)
                {
                    if(!empty($r['name']))
                    {
                        $translation->__set($r['name'], $r['translation']);
                    }
                }
                $translation->initialized = true;
            }
        }
        return $translation;
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
            throw new \Exception(__METHOD__ . ' must not be emtpy');
        }

        $local = include($_SERVER['DOCUMENT_ROOT'].'/../config/autoload/local.php');
        $db = new Adapter($local['db']);
        $stmt = $db->createStatement();
        $stmt->prepare($query);
        $result = $stmt->execute();
        $arr = array();
        if($returnResults)
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
     * @see /vendor/Custom/Plugins/Password.php
     * @param null|string $password the user password in plain text
     * @return array contains salt, and the encrypted password with that salt
     * @todo add default php password_hash implementation
     */
    public static function createPassword($password = null)
    {
        require '/vendor/Custom/Plugins/Password.php';

        if (empty($password))
        {
            throw new Exception("Password cannot be empty");
        }
        if (self::strLength($password) < 8)
        {
            throw new Exception("Password must be atleast 8 characters long");
        }
        return password_hash($password, PASSWORD_BCRYPT, array("cost" => static::$bcryptCost));
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
            $str = Rand::getBytes($number, true);
            $token = base64_encode($str);
            if (self::strLength($token) === 64)
            {
                return $token;
            }
            throw new Exception("Error while generating a token");
        }
        throw new Exception("Error while generating a token");
    }
}
?>
