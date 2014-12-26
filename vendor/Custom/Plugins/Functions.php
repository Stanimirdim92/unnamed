<?php
namespace Custom\Plugins;

use Zend\Session\Container;
use Zend\Db\Adapter\Driver\Pdo\Statement;
use Zend\Db\Adapter\Adapter;
use Zend\Math\Rand;

require '/vendor/Custom/Plugins/Password.php';

class Functions
{
    /**
     * @var string $bcryptCost
     */
    private static $bcryptCost = 13;

    /**
     * @var bool $translation
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
        if (!$language || !is_int($language))
        {
            throw new \Exception("Language id must be a number");
        }

        $total = self::getSimpleQueryResults("SELECT COUNT(id) FROM `language` WHERE `active`='1'");

        if($language < 1 || $language > $total)
        {
            $language = 1;
        }
        
        static::$translation = new Container('translations');
        if(!isset(static::$translation->initialized) || $reload)
        {
            $query = "SELECT `termtranslation`.`translation`, `term`.`name`
                    FROM `term` INNER JOIN `termtranslation` ON `term`.`id`=`termtranslation`.`term`
                    WHERE `termtranslation`.`language`='{$language}'
                    ORDER BY `term`.`name` ASC";
            $result = self::getSimpleQueryResults($query);
            if (count($result) > 0)
            {
                foreach($result as $r)
                {
                    if(!empty($r['name']))
                    {
                        static::$translation->__set($r['name'], $r['translation']);
                    }
                }
                static::$translation->initialized = true;
            }
        }
        return static::$translation;
    }

    /**
     * Create plain mysql queries.
     *
     * @param String $query
     * @param Bool $returnResults
     * @return Adapter|ResultSet
     */
    public static function getSimpleQueryResults($query = null, $returnResults = true)
    {
        $query = trim($query);
        if (empty($query))
        {
            $returnResults = false;
            throw new \Exception(__METHOD__ . ' must not be emtpy');
        }
        if (!is_string($query))
        {
            $query = (string) $query;
        }

        $local = include($_SERVER['DOCUMENT_ROOT'].'/../config/autoload/local.php');
        // $config = array_merge($local['db'], $global['db']);
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
        if (empty($password) && self::strLength($password) < 5)
        {
            throw new Exception("Password cannot be empty");
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
        if (is_int($number) && $number === 48)
        {
            $str = Rand::getBytes($number, true);
            $token = base64_encode($str);
            if (self::strLength($token) === 64)
            {
                return $token;
            }
            $cache->error = "Generated token must be 64 characters long";
            return $this->redirect()->toUrl("/");
        }
        $cache->error = "Error while generating a token";
        return $this->redirect()->toUrl("/");
    }
}
?>
