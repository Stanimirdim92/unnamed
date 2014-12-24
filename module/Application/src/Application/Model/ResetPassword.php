<?php
namespace Application\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\ServiceManager\ServiceManager;

class ResetPassword
{
    private $_inputFilter;

    /**
     * ServiceManager is a dependency injection we use for any additional methods requiring DB access.
     * Please, note that this is not the best way, but it does the job.
     *
     * @var $_serviceManager ServiceManager 
     */
    private $_serviceManager; 

    /**
     * @param Int $_id
     * @return int
     */
    private $_id;

    /**
     * @param string $_password
     * @return string
     */
    private $_token;

    /**
     * @param string $_ip
     * @return string
     */
    private $_ip;

    /**
     * @param string $_date
     * @return string
     */
    private $_date;

    /**
     * @param int $_user
     * @return int
     */
    private $_user;

    public function setServiceManager(ServiceManager $sm)
    {
        $this->_serviceManager = $sm;
    }

    public function exchangeArray($data)
    {
        $this->_id = (isset($data['id'])) ? $data['id'] : null;
        $this->_ip = (isset($data['ip'])) ? $data['ip'] : null;
        $this->_date = (isset($data['date'])) ? $data['date'] : null;
        $this->_token = (isset($data['token'])) ? $data['token'] : null;
        $this->_user = (isset($data['user'])) ? $data['user'] : null;
    }

    /**
     * constructor
     */
    public function __construct(array $options = null, ServiceManager $sm = null)
    {
        if (is_array($options) && $options instanceof Traversable)
        {
            $this->exchangeArray($options);
        }
        if($sm != null)
        {
            $this->_serviceManager = $sm;
        }
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
    public function setId(int $id)
    {
        $this->_id = $id;
    }

    /**
    * Set token
    * @param String $token 
    */
    public function setToken($token)
    {
        $this->_token = $token;
    }

    /**
    * Get token
    * @return String
    */
    public function getToken()
    {
        return $this->_password;
    }

    /**
    * Set ip
    * @param String $ip 
    */
    public function setIp($ip)
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
    public function setDate($date)
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
    public function setUser($user)
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
        try
        {
            return $this->serviceManager->get('UserTable')->getUser($this->_user);
        }
        catch (\Exception $e)
        {
            return null;
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
        if (property_exists($this, '_'. $property))
        {
            $this->{'_'. $property} = $value;
        }
    }

    /**
     * magic property exists (isset) checker
     */
    public function __isset($property)
    {
        return (property_exists($this, '_'. $property));
    }
    
    /**
     * magic serializer
     */
    public function __sleep()
    {
      	$skip = array("_serviceManager");
      	$returnValue = array();
        $data = get_class_vars(get_class($this));
        foreach($data as $key=>$value)
        {
            if (!in_array($key,$skip))
            {
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
    public function getProperties($skip=array("_serviceManager"))
    {
        $returnValue = array();
        $data = get_class_vars(get_class($this));
        foreach($data as $key => $value)
        {
            if (!in_array($key,$skip))
            {
                $returnValue[$key]=$this->$key;
            }
        }
        return $returnValue;
    }
    /**
     * encode this object as json, we do not include the mapper properties
     */
    public function toJson()
    {
        return \Zend\Json\Json::encode($this->getProperties());
    }
}