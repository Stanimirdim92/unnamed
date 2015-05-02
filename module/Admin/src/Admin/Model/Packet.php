<?php
namespace Admin\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\ServiceManager\ServiceManager;


class Packet implements InputFilterAwareInterface
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


    private $_diskspace;


    private $_bandwidth;


    private $_domains;


    private $_dedictip;


    private $_domainreg;


    private $_support;


    private $_webeditor;

    private $_price;
    

    private $_type;
    

    private $_text;
    

    private $_discount;


    private $_language;
    private $_dollar;
    private $_euro;
    
    
    public function setServiceManager($sm)
    {
        $this->_serviceManager = $sm;
    }

    public function exchangeArray($data)
    {
        $this->_id = (isset($data['id'])) ? $data['id'] : null;
        $this->_diskspace = (isset($data['diskspace'])) ? $data['diskspace'] : null;
        $this->_bandwidth = (isset($data['bandwidth'])) ? $data['bandwidth'] : null;
        $this->_domains = (isset($data['domains'])) ? $data['domains'] : null;
        $this->_dedictip = (isset($data['dedictip'])) ? $data['dedictip'] : null;
        $this->_domainreg = (isset($data['domainreg'])) ? $data['domainreg'] : null;
        $this->_support = (isset($data['support'])) ? $data['support'] : null;
        $this->_webeditor = (isset($data['webeditor'])) ? $data['webeditor'] : null;
        $this->_price = (isset($data['price'])) ? $data['price'] : null;
        $this->_type = (isset($data['type'])) ? $data['type'] : null;
        $this->_text = (isset($data['text'])) ? $data['text'] : null;
        $this->_discount = (isset($data['discount'])) ? $data['discount'] : null;
        $this->_language = (isset($data['language'])) ? $data['language'] : null;
        $this->_dollar = (isset($data['dollar'])) ? $data['dollar'] : null;
        $this->_euro = (isset($data['euro'])) ? $data['euro'] : null;
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
     * Set DiskSpace
     * @param string $diskspace
     */
    public function setDiskSpace($diskspace)
    {
        $this->_diskspace = $diskspace;
    }

    /**
     * Get diskspace
     * @return int
     */
    public function getDiskSpace()
    {
        return $this->_diskspace;
    }
    
    /**
     * Set bandwidth
     * @param String $bandwidth
     */
    public function setBandWidth($bandwidth)
    {
        $this->_bandwidth = $bandwidth;
    }

    /**
     * Get bandwidth
     * @return String
     */
    public function getBandWidth()
    {
        return $this->_bandwidth;
    }
     
    /**
     * Set domains
     * @param String $domains
     */
    public function setDomains($domains)
    {
        $this->_domains = $domains;
    }

    /**
     * Get domains
     * @return String
     */
    public function getDomains()
    {
        return $this->_domains;
    }
     
    /**
     * Set dedictip
     * @param int $dedictip
     */
    public function setDedictIp($dedictip)
    {
        $this->_dedictip = $dedictip;
    }

    /**
     * Get dedictip
     * @return int
     */
    public function getDedictIp()
    {
        return $this->_dedictip;
    }
     
    /**
     * Set domainreg
     * @param String $domainreg
     */
    public function setDomainReg($domainreg)
    {
        $this->_domainreg = $domainreg;
    }

    /**
     * Get domainreg
     * @return String
     */
    public function getDomainReg()
    {
        return $this->_domainreg;
    }
     
    /**
     * Set support
     * @param string $support
     */
    public function setSupport($support)
    {
        $this->_support = $support;
    }

    /**
     * Get support
     * @return string
     */
    public function getSupport()
    {
        return $this->_support;
    }

    /**
     * Set webeditor
     * @param string $webeditor
     */
    public function setWebEditor($webeditor)
    {
        $this->_webeditor = $webeditor;
    }

    /**
     * Get webeditor
     * @return string
     */
    public function getWebEditor()
    {
        return $this->_webeditor;
    }

    /**
     * Set price
     * @param string $price
     */
    public function setPrice($price)
    {
        $this->_price = $price;
    }

    /**
     * Get price
     * @return string
     */
    public function getPrice()
    {
        return $this->_price;
    }
     
    /**
     * Set type
     * @param int $type
     */
    public function setType($type)
    {
        $this->_type = $type;
    }

    /**
     * Get type
     * @return int
     */
    public function getType()
    {
        return $this->_type;
    }

    public function getTypeByName()
    {
        if ($this->_type == 0) {
            return "Basic packet";
        } elseif ($this->_type == 1) {
            return "Normal packet";
        } elseif ($this->_type == 2) {
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
        $this->_text = $text;
    }

    /**
     * Get text
     * @return int
     */
    public function getText()
    {
        return $this->_text;
    }

    /**
     * Set discount
     * @param int $discount
     */
    public function setDiscount($discount)
    {
        $this->_discount = $discount;
    }

    /**
     * Get discount
     * @return int
     */
    public function getDiscount()
    {
        return $this->_discount;
    }

    /**
     * Set language
     * @param int $language
     */
    public function setLanguage($language)
    {
        $this->_language = $language;
    }

    /**
     * Get language
     * @return int
     */
    public function getLanguage()
    {
        return $this->_language;
    }

    /**
     * Set dollar
     * @param int $dollar
     */
    public function setDollar($dollar)
    {
        $this->_dollar = $dollar;
    }

    /**
     * Get dollar
     * @return int
     */
    public function getDollar()
    {
        return $this->_dollar;
    }

    /**
     * Set euro
     * @param int $euro
     */
    public function setEuro($euro)
    {
        $this->_euro = $euro;
    }

    /**
     * Get euro
     * @return int
     */
    public function getEuro()
    {
        return $this->_euro;
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
            $inputFilter->add(array(
                'name'     => 'id',
                'required' => false,
                'filters'  => array(
                    array('name' => 'Int'),
                ),
            ));

            $inputFilter->add(array(
                "name"=>"diskspace",
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
                "name"=>"bandwidth",
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
                "name"=>"domains",
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
                "name"=>"dedictip",
                "required" => true,
                'filters' => array(
                    array('name' => 'Int'),
                ),
                'validators' => array(
                    array('name' => 'NotEmpty'),
                ),
            ));
            $inputFilter->add(array(
                "name"=>"domainreg",
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
                "name"=>"support",
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
                "name"=>"webeditor",
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
                "name"=>"price",
                "required" => true,
                'filters' => array(
                    array('name' => 'Int'),
                ),
                'validators' => array(
                    array('name' => 'NotEmpty'),
                    array('name' => 'Float'),
                ),
            ));
            $inputFilter->add(array(
                "name"=>"type",
                "required" => true,
                'filters' => array(
                    array('name' => 'Int'),
                ),
                'validators' => array(
                    array('name' => 'NotEmpty'),
                ),
            ));
            $inputFilter->add(array(
                "name"=>"text",
                "required" => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
            ));
            $inputFilter->add(array(
                "name"=>"discount",
                "required" => false,
                'filters' => array(
                    array('name' => 'Int'),
                ),
            ));
            $inputFilter->add(array(
                "name"=>"language",
                "required" => false,
                'filters' => array(
                    array('name' => 'Int'),
                ),
            ));
            $inputFilter->add(array(
                "name"=>"dollar",
                "required" => true,
                'filters' => array(
                    array('name' => 'Float'),
                ),
            ));
            $inputFilter->add(array(
                "name"=>"euro",
                "required" => true,
                'filters' => array(
                    array('name' => 'Float'),
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
        $clone->setDiskSpace($this->_diskspace);
        $clone->setBandWidth($this->_bandwidth);
        $clone->setDomains($this->_domains);
        $clone->setDedictIp($this->_dedictip);
        $clone->setDomainReg($this->_domainreg);
        $clone->setSupport($this->_support);
        $clone->setWebEditor($this->_webeditor);
        $clone->setPrice($this->_price);
        $clone->setType($this->_type);
        $clone->setText($this->_text);
        $clone->setDiscount($this->_discount);
        $clone->setLanguage($this->_language);
        $clone->setDollar($this->_dollar);
        $clone->setEuro($this->_euro);
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
