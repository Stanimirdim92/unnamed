<?php
namespace Admin\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;
use Zend\ServiceManager\ServiceManager;

class Purchase
{
    /**
     * ServiceManager is a dependency injection we use for any additional methods requiring DB access.
     * Please, note that this is not the best way, but it does the job.
     *
     * @var $serviceManager ServiceManager
     */
    private $serviceManager;

    /**
     * @param Int $id
     * @return int
     */
    private $id;



    /**
     * @param Int $packet
     * @return int
     */
    private $packet;

    /**
     * @param Date $packetexpires
     * @return string
     */
    private $packetexpires;

    /**
     * @param Int $user
     * @return int
     */
    private $user;

    /**
     * @param Int $purchasedate
     * @return int
     */
    private $purchasedate;

    /**
     * @param Bool $payed
     * @return int
     */
    private $payed;

    /**
     * @param Bool $active
     * @return int
     */
    private $active;

    /**
     * @param Int $money
     * @return int
     */
    private $money;

    /**
     * @param Int $money
     * @return int
     */
    private $currency;

    public function setServiceManager($sm)
    {
        $this->serviceManager = $sm;
    }

    public function exchangeArray($data)
    {
        $this->id = (isset($data['id'])) ? $data['id'] : null;
        $this->purchasedate = (isset($data['purchasedate'])) ? $data['purchasedate'] : null;
        $this->user = (isset($data['user'])) ? $data['user'] : null;
        $this->packet = (isset($data['packet'])) ? $data['packet'] : null;
        $this->packetexpires = (isset($data['packetexpires'])) ? $data['packetexpires'] : null;
        $this->payed = (isset($data['payed'])) ? $data['payed'] : null;
        $this->active = (isset($data['active'])) ? $data['active'] : null;
        $this->money = (isset($data['money'])) ? $data['money'] : null;
        $this->currency = (isset($data['currency'])) ? $data['currency'] : null;
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
            $this->serviceManager = $sm;
        }
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
    public function setId(int $id)
    {
        $this->id = $id;
    }


    /**
     * Set user
     * @param String $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * Get user
     * @return String
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set packet
     * @param Int $packet
     */
    public function setPacket($packet)
    {
        $this->packet = $packet;
    }

    /**
     * Get packet
     * @return Int
     */
    public function getPacket()
    {
        return $this->packet;
    }

    /**
     * Set packetexpires
     * @param string $packetexpires
     */
    public function setPacketExpireMonths($packetexpires)
    {
        $this->packetexpires = $packetexpires;
    }

    /**
     * Get packetexpires
     * @return string
     */
    public function getPacketExpireMonths()
    {
        return $this->packetexpires;
    }

    /**
     * Set purchasedate
     * @param string $purchasedate
     */
    public function setPurchaseDate($purchasedate)
    {
        $this->purchasedate = $purchasedate;
    }

    /**
     * Get purchasedate
     * @return string
     */
    public function getPurchaseDate()
    {
        return $this->purchasedate;
    }

    /**
     * Set payed
     * @param bool $payed
     */
    public function setPayed($payed)
    {
        $this->payed = $payed;
    }

    /**
     * Get payed
     * @return bool
     */
    public function getPayed()
    {
        return $this->payed;
    }

    /**
     * Set active
     * @param bool $active
     */
    public function setActive($active)
    {
        $this->active = $active;
    }

    /**
     * Get active
     * @return bool
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Set money
     * @param int $money
     */
    public function setMoney($money)
    {
        $this->money = $money;
    }

    /**
     * Get money
     * @return int
     */
    public function getMoney()
    {
        return $this->money;
    }

    /**
     * Set currency
     * @param int $currency
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
    }

    /**
     * Get currency
     * @return int
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * Get the related object from the DB
     */
    public function getUserObject()
    {
        try {
            return $this->serviceManager->get('UserTable')->getUser($this->user);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Get the related object from the DB
     */
    public function getPacketObject()
    {
        try {
            return $this->serviceManager->get('PacketTable')->getPacket($this->packet);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Get the related object from the DB
     */
    public function getCurrencyObject()
    {
        try {
            return $this->serviceManager->get('CurrencyTable')->getCurrency($this->currency);
        } catch (\Exception $e) {
            return null;
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
        return (property_exists($this, $property));
    }

    /**
     * magic serializer
     */
    public function __sleep()
    {
        $skip = ["serviceManager"];
        $returnValue = [];
        $data = get_class_vars(get_class($this));
        foreach ($data as $key=>$value) {
            if (!in_array($key, $skip)) {
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
    public function getProperties($skip=["serviceManager"])
    {
        $returnValue = [];
        $data = get_class_vars(get_class($this));
        foreach ($data as $key=>$value) {
            if (!in_array($key, $skip)) {
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
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();
            $inputFilter->add([
                'name'     => 'id',
                'required' => false,
                'filters'  => [
                    ['name' => 'Int'],
                ],
            ]);

            $inputFilter->add([
                "name"=>"user",
                "required" => true,
                'filters'  => [
                    ['name' => 'Int'],
                ],
                'validators' => [
                    ['name' => 'NotEmpty'],
                ],
            ]);
            $inputFilter->add([
                "name"=>"purchasedate",
                "required" => true,
                'filters' => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                ],
                'validators' => [
                    ['name' => 'NotEmpty'],
                ],
            ]);
            $inputFilter->add([
                "name"=>"packet",
                "required" => true,
                'filters'  => [
                    ['name' => 'Int'],
                ],
                'validators' => [
                    ['name' => 'NotEmpty'],
                ],
            ]);
            $inputFilter->add([
                "name"=>"packetexpires",
                "required" => true,
                'filters' => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                ],
                'validators' => [
                    ['name' => 'NotEmpty'],
                ],
            ]);
            $inputFilter->add([
                "name"=>"payed",
                "required" => true,
                'filters'  => [
                    ['name' => 'Boolean'],
                ],
                'validators' => [
                    ['name' => 'NotEmpty'],
                ],
            ]);
            $inputFilter->add([
                "name"=>"active",
                "required" => true,
                'filters'  => [
                    ['name' => 'Boolean'],
                ],
                'validators' => [
                    ['name' => 'NotEmpty'],
                ],
            ]);
            $inputFilter->add([
                "name"=>"money",
                "required" => true,
                'filters'  => [
                    ['name' => 'Float'],
                ],
                'validators' => [
                    ['name' => 'NotEmpty'],
                ],
            ]);
            $inputFilter->add([
                "name"=>"currency",
                "required" => true,
                'filters'  => [
                    ['name' => 'Int'],
                ],
                'validators' => [
                    ['name' => 'NotEmpty'],
                ],
            ]);
            $this->inputFilter = $inputFilter;
        }
        return $this->inputFilter;
    }
}
