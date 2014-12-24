<?php
namespace Admin\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\ServiceManager\ServiceManager;

class Administrator implements InputFilterAwareInterface
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
     * @param Int $_user
     * @return Int
     */
    private $_user;

    public function setServiceManager($sm)
    {
        $this->_serviceManager = $sm;
    }

    public function exchangeArray($data)
    {
        $this->_id = (isset($data['id'])) ? $data['id'] : null;
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
    * Set User
    * @param String $user 
    */
    public function setUser($user)
    {
        $this->_user = $user;
    }

    /**
    * Get user
    * @return String
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
            return $this->serviceManager->get('UserTable')->getUser("{$this->_user}");
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
        foreach($data as $key=>$value)
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
    
    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }
    
    public function getInputFilter()
    {
        if (!$this->_inputFilter) 
        {
            $inputFilter = new InputFilter();
            $inputFilter->add(array(
                'name'     => 'id',
                'required' => false,
                'filters'  => array(
                    array('name' => 'Int'),
                ),
            ));

            $inputFilter->add(array(
                "name"=>"user",
                "required" => true,
                'filters' => array(
                    array('name' => 'Int'),
                ),
            ));
            $this->_inputFilter = $inputFilter;
        }
        return $this->_inputFilter;
    }
    
    /**
     * This method is a copy constructor that will return a copy object (except for the id field)
     * Note that this method will not save the object
     */
    public function getCopy()
    {
        $clone = new self();
        $clone->setUser($this->_user);
        return $clone;
    }
}
