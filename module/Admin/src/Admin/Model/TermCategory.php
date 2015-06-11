<?php
namespace Admin\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class TermCategory implements InputFilterAwareInterface
{
    private $inputFilter;

    /**
     * ServiceManager is a dependency injection we use for any additional methods requiring DB access
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
     * @param String $name
     * @return string
     */
    private $name;

    public function setServiceLocator($sm)
    {
        $this->serviceManager = $sm;
    }

    public function exchangeArray($data)
    {
        $this->id = (isset($data['id'])) ? $data['id'] : null;
        $this->name = (isset($data['name'])) ? $data['name'] : null;
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
     * Set name
     * @param String $name
     */
    public function setName($name)
    {
        $this->name = (String) $name;
    }

    /**
     * Get name
     * @return null|String
     */
    public function getName()
    {
        return $this->name;
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
                'name' => 'id',
                'required' => false,
                'filters' => [
                    ['name' => 'Int'],
                ],
            ]);

            $inputFilter->add([
                "name"=>"name",
                'required' => true,
                'filters' => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                ],
                'validators' => [
                    [
                        'name' => 'StringLength',
                        'options' => [
                            'encoding' => 'UTF-8',
                        ],
                    ],
                    ['name' => 'NotEmpty'],
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
        $clone->setName($this->name);
        return $clone;
    }

    /**
     * toString method
     */
    public function toString()
    {
        return $this->name;
    }
}
