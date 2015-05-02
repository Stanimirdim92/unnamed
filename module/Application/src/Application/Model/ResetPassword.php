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
 * @package    ZendPress
 * @author     Stanimir Dimitrov <stanimirdim92@gmail.com>
 * @copyright  2015 Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 * @version    0.03
 * @link       TBA
 */

namespace Application\Model;

use Zend\ServiceManager\ServiceManager;

class ResetPassword
{
    /**
     * ServiceManager is a dependency injection we use for any additional methods requiring DB access.
     * Please, note that this is not the best way, but it does the job.
     *
     * @var $_serviceManager ServiceManager
     */
    private $_serviceManager = null;

    /**
     * @param Int $_id
     * @return int
     */
    private $_id = 0;

    /**
     * @param string $_password
     * @return string
     */
    private $_token = null;

    /**
     * @param string $_ip
     * @return string
     */
    private $_ip = null;

    /**
     * @param string $_date
     * @return string
     */
    private $_date = "0000-00-00 00:00:00";

    /**
     * @param int $_user
     * @return int
     */
    private $_user = 0;

    /**
     * @param null $sm
     * @return ServiceManager
     */
    public function setServiceManager(ServiceManager $sm = null)
    {
        $this->_serviceManager = $sm;
    }

    /**
     * @var array $data
     * @return mixed
     */
    public function exchangeArray(array $data = array())
    {
        $this->_id = (isset($data['id'])) ? $data['id'] : 0;
        $this->_ip = (isset($data['ip'])) ? $data['ip'] : null;
        $this->_date = (isset($data['date'])) ? $data['date'] : "0000-00-00 00:00:00";
        $this->_token = (isset($data['token'])) ? $data['token'] : null;
        $this->_user = (isset($data['user'])) ? $data['user'] : 0;
    }

    /**
     * constructor
     */
    public function __construct(array $options = array(), ServiceManager $sm = null)
    {
        $this->exchangeArray($options);
        $this->_serviceManager = $sm;
    }
    
    /**
     * Get id
     */
    public function getId()
    {
        return $this->_id;
    }
    
    /**
     * Set id
     * @param int
     */
    public function setId($id = 0)
    {
        $this->_id = $id;
    }

    /**
     * Set token
     * @param String $token
     */
    public function setToken($token = null)
    {
        $this->_token = $token;
    }

    /**
     * Get token
     * @return String
     */
    public function getToken()
    {
        return $this->_token;
    }

    /**
     * Set ip
     * @param String $ip
     */
    public function setIp($ip = null)
    {
        $this->_ip = $ip;
    }

    /**
     * Get ip
     * @return String
     */
    public function getIp()
    {
        return $this->_ip;
    }

    /**
     * Set date
     * @param Int $date
     */
    public function setDate($date = "0000-00-00 00:00:00")
    {
        $this->_date = $date;
    }

    /**
     * Get date
     * @return Int
     */
    public function getDate()
    {
        return $this->_date;
    }

    /**
     * Set user
     * @param Int $user
     */
    public function setUser($user = null)
    {
        $this->_user = $user;
    }

    /**
     * Get user
     * @return Int
     */
    public function getUser()
    {
        return $this->_user;
    }

    /**
     * Get the related object from the DB
     */
    public function getUserObject()
    {
        try {
            return $this->serviceManager->get('UserTable')->getUser($this->_user);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * magic getter
     */
    public function __get($property)
    {
        return (property_exists($this, '_'. $property) ? $this->{'_'. $property} : null);
    }

    /**
     * magic setter
     */
    public function __set($property, $value)
    {
        if (property_exists($this, '_'. $property)) {
            $this->{'_'. $property} = $value;
        }
    }

    /**
     * magic property exists (isset) checker
     */
    public function __isset($property)
    {
        return (property_exists($this, '_'. $property) ? isset($this->{'_'. $property}) : null);
    }
    
    /**
     * magic serializer
     */
    public function __sleep()
    {
        $skip = array("_serviceManager");
        $returnValue = array();
        $data = get_class_vars(get_class($this));
        foreach ($data as $key=>$value) {
            if (!in_array($key,$skip)) {
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
     * this is a handy function for encoding the object to json for transfer purposes
     */
    public function getProperties(array $skip = array("_serviceManager"), $serializable = false)
    {
        $returnValue = array();
        $data = get_class_vars(get_class($this));
        foreach ($data as $key => $value) {
            if (!in_array($key, $skip)) {
                $returnValue[$key] = $this->$key;
            }
        }
        if ($serializable) {
            return serialize($returnValue);
        }
        return $returnValue;
    }
}
