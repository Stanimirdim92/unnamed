<?php
namespace Admin\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\ServiceManager\ServiceManager;


class Packet implements InputFilterAwareInterface
{
    private $inputFilter;

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


    private $diskspace;


    private $bandwidth;


    private $domains;


    private $dedictip;


    private $domainreg;


    private $support;


    private $webeditor;

    private $price;


    private $type;


    private $text;


    private $discount;


    private $language;
    private $dollar;
    private $euro;


    public function setServiceManager($sm)
    {
        $this->serviceManager = $sm;
    }

    public function exchangeArray($data)
    {
        $this->id = (isset($data['id'])) ? $data['id'] : null;
        $this->diskspace = (isset($data['diskspace'])) ? $data['diskspace'] : null;
        $this->bandwidth = (isset($data['bandwidth'])) ? $data['bandwidth'] : null;
        $this->domains = (isset($data['domains'])) ? $data['domains'] : null;
        $this->dedictip = (isset($data['dedictip'])) ? $data['dedictip'] : null;
        $this->domainreg = (isset($data['domainreg'])) ? $data['domainreg'] : null;
        $this->support = (isset($data['support'])) ? $data['support'] : null;
        $this->webeditor = (isset($data['webeditor'])) ? $data['webeditor'] : null;
        $this->price = (isset($data['price'])) ? $data['price'] : null;
        $this->type = (isset($data['type'])) ? $data['type'] : null;
        $this->text = (isset($data['text'])) ? $data['text'] : null;
        $this->discount = (isset($data['discount'])) ? $data['discount'] : null;
        $this->language = (isset($data['language'])) ? $data['language'] : null;
        $this->dollar = (isset($data['dollar'])) ? $data['dollar'] : null;
        $this->euro = (isset($data['euro'])) ? $data['euro'] : null;
    }

    /**
     * constructor
     */
    public function __construct(array $options = null, ServiceManager $sm=null)
    {
        if (is_array($options) && $options instanceof Traversable) {
            $this->exchangeArray($options);
        }
        if ($sm!=null) {
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
     * Set DiskSpace
     * @param string $diskspace
     */
    public function setDiskSpace($diskspace)
    {
        $this->diskspace = $diskspace;
    }

    /**
     * Get diskspace
     * @return int
     */
    public function getDiskSpace()
    {
        return $this->diskspace;
    }

    /**
     * Set bandwidth
     * @param String $bandwidth
     */
    public function setBandWidth($bandwidth)
    {
        $this->bandwidth = $bandwidth;
    }

    /**
     * Get bandwidth
     * @return String
     */
    public function getBandWidth()
    {
        return $this->bandwidth;
    }

    /**
     * Set domains
     * @param String $domains
     */
    public function setDomains($domains)
    {
        $this->domains = $domains;
    }

    /**
     * Get domains
     * @return String
     */
    public function getDomains()
    {
        return $this->domains;
    }

    /**
     * Set dedictip
     * @param int $dedictip
     */
    public function setDedictIp($dedictip)
    {
        $this->dedictip = $dedictip;
    }

    /**
     * Get dedictip
     * @return int
     */
    public function getDedictIp()
    {
        return $this->dedictip;
    }

    /**
     * Set domainreg
     * @param String $domainreg
     */
    public function setDomainReg($domainreg)
    {
        $this->domainreg = $domainreg;
    }

    /**
     * Get domainreg
     * @return String
     */
    public function getDomainReg()
    {
        return $this->domainreg;
    }

    /**
     * Set support
     * @param string $support
     */
    public function setSupport($support)
    {
        $this->support = $support;
    }

    /**
     * Get support
     * @return string
     */
    public function getSupport()
    {
        return $this->support;
    }

    /**
     * Set webeditor
     * @param string $webeditor
     */
    public function setWebEditor($webeditor)
    {
        $this->webeditor = $webeditor;
    }

    /**
     * Get webeditor
     * @return string
     */
    public function getWebEditor()
    {
        return $this->webeditor;
    }

    /**
     * Set price
     * @param string $price
     */
    public function setPrice($price)
    {
        $this->price = $price;
    }

    /**
     * Get price
     * @return string
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set type
     * @param int $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * Get type
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    public function getTypeByName()
    {
        if ($this->type == 0) {
            return "Basic packet";
        } elseif ($this->type == 1) {
            return "Normal packet";
        } elseif ($this->type == 2) {
            return "Optima packet";
        } else {
            return "Expert packet";
        }
    }

    /**
     * Set text
     * @param int $text
     */
    public function setText($text)
    {
        $this->text = $text;
    }

    /**
     * Get text
     * @return int
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set discount
     * @param int $discount
     */
    public function setDiscount($discount)
    {
        $this->discount = $discount;
    }

    /**
     * Get discount
     * @return int
     */
    public function getDiscount()
    {
        return $this->discount;
    }

    /**
     * Set language
     * @param int $language
     */
    public function setLanguage($language)
    {
        $this->language = $language;
    }

    /**
     * Get language
     * @return int
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * Set dollar
     * @param int $dollar
     */
    public function setDollar($dollar)
    {
        $this->dollar = $dollar;
    }

    /**
     * Get dollar
     * @return int
     */
    public function getDollar()
    {
        return $this->dollar;
    }

    /**
     * Set euro
     * @param int $euro
     */
    public function setEuro($euro)
    {
        $this->euro = $euro;
    }

    /**
     * Get euro
     * @return int
     */
    public function getEuro()
    {
        return $this->euro;
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
                "name"=>"diskspace",
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
                "name"=>"bandwidth",
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
                "name"=>"domains",
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
                "name"=>"dedictip",
                "required" => true,
                'filters' => [
                    ['name' => 'Int'],
                ],
                'validators' => [
                    ['name' => 'NotEmpty'],
                ],
            ]);
            $inputFilter->add([
                "name"=>"domainreg",
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
                "name"=>"support",
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
                "name"=>"webeditor",
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
                "name"=>"price",
                "required" => true,
                'filters' => [
                    ['name' => 'Int'],
                ],
                'validators' => [
                    ['name' => 'NotEmpty'],
                    ['name' => 'Float'],
                ],
            ]);
            $inputFilter->add([
                "name"=>"type",
                "required" => true,
                'filters' => [
                    ['name' => 'Int'],
                ],
                'validators' => [
                    ['name' => 'NotEmpty'],
                ],
            ]);
            $inputFilter->add([
                "name"=>"text",
                "required" => true,
                'filters' => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                ],
            ]);
            $inputFilter->add([
                "name"=>"discount",
                "required" => false,
                'filters' => [
                    ['name' => 'Int'],
                ],
            ]);
            $inputFilter->add([
                "name"=>"language",
                "required" => false,
                'filters' => [
                    ['name' => 'Int'],
                ],
            ]);
            $inputFilter->add([
                "name"=>"dollar",
                "required" => true,
                'filters' => [
                    ['name' => 'Float'],
                ],
            ]);
            $inputFilter->add([
                "name"=>"euro",
                "required" => true,
                'filters' => [
                    ['name' => 'Float'],
                ],
            ]);
            $this->inputFilter = $inputFilter;
        }
        return $this->inputFilter;
    }

    /**
     * This method is a copy constructor that will return a copy object (except for the id field)
     * Note that this method will not save the object
     */
    public function getCopy()
    {
        $clone = new self();
        $clone->setDiskSpace($this->diskspace);
        $clone->setBandWidth($this->bandwidth);
        $clone->setDomains($this->domains);
        $clone->setDedictIp($this->dedictip);
        $clone->setDomainReg($this->domainreg);
        $clone->setSupport($this->support);
        $clone->setWebEditor($this->webeditor);
        $clone->setPrice($this->price);
        $clone->setType($this->type);
        $clone->setText($this->text);
        $clone->setDiscount($this->discount);
        $clone->setLanguage($this->language);
        $clone->setDollar($this->dollar);
        $clone->setEuro($this->euro);
        return $clone;
    }

    /**
     * toString method
     */
    public function toString()
    {
        return $this->getTypeByName();
    }
}
