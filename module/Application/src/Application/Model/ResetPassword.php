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
 * @category   Application\ResetPassword
 * @package    Unnamed
 * @author     Stanimir Dimitrov <stanimirdim92@gmail.com>
 * @copyright  2015 Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 * @version    0.0.3
 * @link       TBA
 */

namespace Application\Model;

use Zend\ServiceManager\ServiceLocatorInterface;

class ResetPassword
{
    /**
     * @var $serviceLocator ServiceManager
     */
    private $serviceLocator = null;

    /**
     * @param Int $id
     * @return int
     */
    private $id = 0;

    /**
     * @param string $password
     * @return string
     */
    private $token = null;

    /**
     * @param string $ip
     * @return string
     */
    private $ip = null;

    /**
     * @param string $date
     * @return string
     */
    private $date = "0000-00-00 00:00:00";

    /**
     * @param int $user
     * @return int
     */
    private $user = 0;

    /**
     * @param null $sm ServiceLocatorInterface|ServiceManager
     * @return ServiceLocatorInterface|ServiceManager|null
     */
    public function setServiceLocator(ServiceLocatorInterface $sm = null)
    {
        $this->serviceLocator = $sm;
    }

    /**
     * @return ServiceLocatorInterface|ServiceManager
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    /**
     * @var array $data
     * @return mixed
     */
    public function exchangeArray(array $data = [])
    {
        $this->id = (isset($data['id'])) ? $data['id'] : 0;
        $this->ip = (isset($data['ip'])) ? $data['ip'] : null;
        $this->date = (isset($data['date'])) ? $data['date'] : "0000-00-00 00:00:00";
        $this->token = (isset($data['token'])) ? $data['token'] : null;
        $this->user = (isset($data['user'])) ? $data['user'] : 0;
    }

    /**
     * constructor
     */
    public function __construct(array $options = [], ServiceLocatorInterface $sm = null)
    {
        $this->exchangeArray($options);
        $this->setServiceLocator($sm);
    }

    /**
     * Get id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set id
     * @param int
     */
    public function setId($id = 0)
    {
        $this->id = $id;
    }

    /**
     * Set token
     * @param String $token
     */
    public function setToken($token = null)
    {
        $this->token = $token;
    }

    /**
     * Get token
     * @return String
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set ip
     * @param String $ip
     */
    public function setIp($ip = null)
    {
        $this->ip = $ip;
    }

    /**
     * Get ip
     * @return String
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * Set date
     * @param Int $date
     */
    public function setDate($date = "0000-00-00 00:00:00")
    {
        $this->date = $date;
    }

    /**
     * Get date
     * @return Int
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set user
     * @param Int $user
     */
    public function setUser($user = null)
    {
        $this->user = $user;
    }

    /**
     * Get user
     * @return Int
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Get the related object from the DB
     */
    public function getUserObject()
    {
        try {
            return $this->getServiceLocator()->get('UserTable')->getUser($this->user);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * magic getter
     */
    public function __get($property)
    {
        return (property_exists($this, $property) ? $this->{$property} : null);
    }

    /**
     * magic setter
     */
    public function __set($property, $value)
    {
        if (property_exists($this, $property)) {
            $this->{$property} = $value;
        }
    }

    /**
     * magic property exists (isset) checker
     */
    public function __isset($property)
    {
        return (property_exists($this, $property) ? isset($this->{$property}) : null);
    }

    /**
     * magic property remove (unset)
     */
    public function __unset($property)
    {
        return (property_exists($this, $property) ? unset($this->{$property}) : null);
    }

    /**
     * magic serializer
     */
    public function __sleep()
    {
        $returnValue = [];
        $data = get_class_vars(get_class($this));
        foreach ($data as $key => $value) {
            if (!in_array($key, "serviceLocator")) {
                $returnValue[] = $key;
            }
        }
        return $returnValue;
    }

    /**
     * magic unserializer (ideally we should recreate the connection to service manager)
     */
    public function __wakeup()
    {
    }

    /**
     * Serialize object or return it as an array
     *
     * @param  array $skip Remove the unnecessary objects from the array
     * @param  bool $serializable Should the function return a serialized object
     * @return array|string
     */
    public function getProperties(array $skip = [], $serializable = false)
    {
        $returnValue = [];
        $data = get_class_vars(get_class($this));
        foreach ($data as $key => $value) {
            if (!in_array($key, $skip)) {
                $returnValue[$key] = $this->{$key};
            }
        }
        if ((bool) $serializable === true) {
            return serialize($returnValue);
        }
        return $returnValue;
    }
}
