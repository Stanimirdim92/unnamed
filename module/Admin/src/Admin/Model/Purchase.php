<?php
namespace Admin\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\ServiceManager\ServiceManager;

class Purchase
{
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
     * @param Int $_packet
     * @return int
     */
    private $_packet;

    /**
     * @param Date $_packetexpires
     * @return string
     */
    private $_packetexpires;

    /**
     * @param Int $_user
     * @return int
     */
    private $_user;

    /**
     * @param Int $_purchasedate
     * @return int
     */
    private $_purchasedate;

    /**
     * @param Bool $_payed
     * @return int
     */
    private $_payed;

    /**
     * @param Bool $_active
     * @return int
     */
    private $_active;

    /**
     * @param Int $_money
     * @return int
     */
    private $_money;

    /**
     * @param Int $_money
     * @return int
     */
    private $_currency;

    public function setServiceManager($sm)
    {
        $this->_serviceManager = $sm;
    }

    public function exchangeArray($data)
    {
        $this->_id = (isset($data['id'])) ? $data['id'] : null;
        $this->_purchasedate = (isset($data['purchasedate'])) ? $data['purchasedate'] : null;
        $this->_user = (isset($data['user'])) ? $data['user'] : null;
        $this->_packet = (isset($data['packet'])) ? $data['packet'] : null;
        $this->_packetexpires = (isset($data['packetexpires'])) ? $data['packetexpires'] : null;
        $this->_payed = (isset($data['payed'])) ? $data['payed'] : null;
        $this->_active = (isset($data['active'])) ? $data['active'] : null;
        $this->_money = (isset($data['money'])) ? $data['money'] : null;
        $this->_currency = (isset($data['currency'])) ? $data['currency'] : null;
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
    * Set user
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
    * Set packet
    * @param Int $packet 
    */
    public function setPacket($packet)
    {
        $this->_packet = $packet;
    }

    /**
    * Get packet
    * @return Int
    */
    public function getPacket()
    {
        return $this->_packet;
    }

    /**
    * Set packetexpires
    * @param string $packetexpires 
    */
    public function setPacketExpireMonths($packetexpires)
    {
        $this->_packetexpires = $packetexpires;
    }

    /**
    * Get packetexpires
    * @return string
    */
    public function getPacketExpireMonths()
    {
        return $this->_packetexpires;
    }

    /**
    * Set purchasedate
    * @param string $purchasedate 
    */
    public function setPurchaseDate($purchasedate)
    {
        $this->_purchasedate = $purchasedate;
    }

    /**
    * Get purchasedate
    * @return string
    */
    public function getPurchaseDate()
    {
        return $this->_purchasedate;
    }

    /**
    * Set payed
    * @param bool $payed 
    */
    public function setPayed($payed)
    {
        $this->_payed = $payed;
    }

    /**
    * Get payed
    * @return bool
    */
    public function getPayed()
    {
        return $this->_payed;
    }

    /**
    * Set active
    * @param bool $active 
    */
    public function setActive($active)
    {
        $this->_active = $active;
    }

    /**
    * Get active
    * @return bool
    */
    public function getActive()
    {
        return $this->_active;
    }

    /**
    * Set money
    * @param int $money 
    */
    public function setMoney($money)
    {
        $this->_money = $money;
    }

    /**
    * Get money
    * @return int
    */
    public function getMoney()
    {
        return $this->_money;
    }

    /**
    * Set currency
    * @param int $currency 
    */
    public function setCurrency($currency)
    {
        $this->_currency = $currency;
    }

    /**
    * Get currency
    * @return int
    */
    public function getCurrency()
    {
        return $this->_currency;
    }

    /**
    * Get the related object from the DB
    */
    public function getUserObject()
    {
        try
        {
            return $this->serviceManager->get('UserTable')->getUser($this->user);
        }
        catch (\Exception $e)
        {
            return null;
        }
    }

    /**
    * Get the related object from the DB
    */
    public function getPacketObject()
    {
        try
        {
            return $this->serviceManager->get('PacketTable')->getPacket($this->packet);
        }
        catch (\Exception $e)
        {
            return null;
        }
    }

    /**
    * Get the related object from the DB
    */
    public function getCurrencyObject()
    {
        try
        {
            return $this->serviceManager->get('CurrencyTable')->getCurrency($this->currency);
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
                'filters'  => array(
                    array('name' => 'Int'),
                ),
                'validators' => array(
                    array('name' => 'NotEmpty'),
                ),
            ));
            $inputFilter->add(array(
                "name"=>"purchasedate",
                "required" => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array('name' => 'NotEmpty'),
                ),
            ));
            $inputFilter->add(array(
                "name"=>"packet",
                "required" => true,
                'filters'  => array(
                    array('name' => 'Int'),
                ),
                'validators' => array(
                    array('name' => 'NotEmpty'),
                ),
            ));
            $inputFilter->add(array(
                "name"=>"packetexpires",
                "required" => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array('name' => 'NotEmpty'),
                ),
            ));
            $inputFilter->add(array(
                "name"=>"payed",
                "required" => true,
                'filters'  => array(
                    array('name' => 'Boolean'),
                ),
                'validators' => array(
                    array('name' => 'NotEmpty'),
                ),
            ));
            $inputFilter->add(array(
                "name"=>"active",
                "required" => true,
                'filters'  => array(
                    array('name' => 'Boolean'),
                ),
                'validators' => array(
                    array('name' => 'NotEmpty'),
                ),
            ));
            $inputFilter->add(array(
                "name"=>"money",
                "required" => true,
                'filters'  => array(
                    array('name' => 'Float'),
                ),
                'validators' => array(
                    array('name' => 'NotEmpty'),
                ),
            ));
            $inputFilter->add(array(
                "name"=>"currency",
                "required" => true,
                'filters'  => array(
                    array('name' => 'Int'),
                ),
                'validators' => array(
                    array('name' => 'NotEmpty'),
                ),
            ));
            $this->_inputFilter = $inputFilter;
        }
        return $this->_inputFilter;
    }
}