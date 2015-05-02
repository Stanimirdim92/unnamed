<?php
namespace Admin\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\ServiceManager\ServiceManager;


class Currency implements InputFilterAwareInterface
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
     * @param String $_name
     * @return string
     */
    private $_name;

    /**
     * @param Int $_active
     * @return int
     */
    private $_active;

    private $_symbol;
    
    public function setServiceManager($sm)
    {
        $this->_serviceManager = $sm;
    }

    public function exchangeArray($data)
    {
        $this->_id = (isset($data['id'])) ? $data['id'] : null;
        $this->_name = (isset($data['name'])) ? $data['name'] : null;
        $this->_active = (isset($data['active'])) ? $data['active'] : null;
        $this->_symbol = (isset($data['symbol'])) ? $data['symbol'] : null;
    }

    /**
     * constructor
     */
    public function __construct(array $options = null, ServiceManager $sm = null)
    {
        if (is_array($options) && $options instanceof Traversable) {
            $this->exchangeArray($options);
        }
        if ($sm != null) {
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
     * Set name
     * @param String $name
     */
    public function setName($name)
    {
        $this->_name = $name;
    }

    /**
     * Get name
     * @return String
     */
    public function getName()
    {
        return $this->_name;
    }
     
    /**
     * Set active
     * @param Boolean $active
     */
    public function setActive($active)
    {
        $this->_active = $active;
    }

    /**
     * Get active
     * @return Boolean
     */
    public function getActive()
    {
        return $this->_active;
    }

    /**
     * Set symbol
     * @param String $symbol
     */
    public function setSymbol($symbol)
    {
        $this->_symbol = $symbol;
    }

    /**
     * Get symbol
     * @return String
     */
    public function getSymbol()
    {
        return $this->_symbol;
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
    public function getProperties($skip=array("_serviceManager"))
    {
        $returnValue = array();
        $data = get_class_vars(get_class($this));
        foreach ($data as $key=>$value) {
            if (!in_array($key,$skip)) {
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
        if (!$this->_inputFilter) {
            $inputFilter = new InputFilter();
            $inputFilter->add(array('name' => 'id', 'required' => false, 'filters' => array(array('name' => 'Int'))));

            $inputFilter->add(array(
                "name"=>"name",
                "required" => false,
                "filters"=> array(array('name' => 'StringTrim')),));
            $inputFilter->add(array(
                "name"=>"active",
                "required" => false, 'filters' => array(array('name' => 'Int')), ));
            $inputFilter->add(array(
                "name"=>"symbol",
                "required" => true,));
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
        $copy = new self();
        $copy->setName($this->_name);
        $copy->setActive($this->_active);
        $copy->setSymbol($this->_symbol);
        return $copy;
    }

    /**
     * toString method
     * @return String
     */
    public function toString()
    {
        return $this->name;
    }
}
