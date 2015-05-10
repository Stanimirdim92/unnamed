<?php
namespace Admin\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\ServiceManager\ServiceManager;

class AdminMenu implements InputFilterAwareInterface
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

    /**
     * @param String $caption
     * @return string
     */
    private $caption;

    /**
     * @param Int $menuOrder
     * @return int
     */
    private $menuOrder;

    /**
     * @param Int $advanced
     * @return int
     */
    private $advanced;

    /**
     * @param String $controller
     * @return string
     */
    private $controller;

    /**
     * @param String $action
     * @return string
     */
    private $action;

    /**
     * @param String $class
     * @return string
     */
    private $class;

    /**
     * @param String $description
     * @return string
     */
    private $description;

    /**
     * @param Int $parent
     * @return int
     */
    private $parent;

    public function setServiceManager($sm)
    {
        $this->serviceManager = $sm;
    }

    public function exchangeArray($data)
    {
        $this->id = (isset($data['id'])) ? $data['id'] : null;
        $this->caption = (isset($data['caption'])) ? $data['caption'] : null;
        $this->menuOrder = (isset($data['menuOrder'])) ? $data['menuOrder'] : null;
        $this->advanced = (isset($data['advanced'])) ? $data['advanced'] : null;
        $this->controller = (isset($data['controller'])) ? $data['controller'] : null;
        $this->action = (isset($data['action'])) ? $data['action'] : null;
        $this->class = (isset($data['class'])) ? $data['class'] : null;
        $this->description = (isset($data['description'])) ? $data['description'] : null;
        $this->parent = (isset($data['parent'])) ? $data['parent'] : null;
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
     * Set caption
     * @param String $caption
     */
    public function setCaption($caption)
    {
        $this->caption = $caption;
    }

    /**
     * Get caption
     * @return String
     */
    public function getCaption()
    {
        return $this->caption;
    }

    /**
     * Set menuOrder
     * @param int $menuOrder
     */
    public function setMenuOrder($menuOrder)
    {
        $this->menuOrder = $menuOrder;
    }

    /**
     * Get menuOrder
     * @return int
     */
    public function getMenuOrder()
    {
        return $this->menuOrder;
    }

    /**
     * Set advanced
     * @param Boolean $advanced
     */
    public function setAdvanced($advanced)
    {
        $this->advanced = $advanced;
    }

    /**
     * Get advanced
     * @return Boolean
     */
    public function getAdvanced()
    {
        return $this->advanced;
    }

    /**
     * Set controller
     * @param String $controller
     */
    public function setController($controller)
    {
        $this->controller = $controller;
    }

    /**
     * Get controller
     * @return String
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * Set action
     * @param String $action
     */
    public function setAction($action)
    {
        $this->action = $action;
    }

    /**
     * Get action
     * @return String
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Set class
     * @param String $class
     */
    public function setClass($class)
    {
        $this->class = $class;
    }

    /**
     * Get class
     * @return String
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * Set description
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * Get description
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set parent
     * @param int $parent
     */
    public function setParent($parent)
    {
        $this->parent = $parent;
    }

    /**
     * Get parent
     * @return int
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Get the related object from the DB
     */
    public function getParentObject()
    {
        try {
            return $this->serviceManager->get('AdminMenuTable')->getAdminMenu("{$this->parent}");
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
            $inputFilter->add(['name' => 'id', 'required' => false, 'filters' => [['name' => 'Int']]]);

            $inputFilter->add([
                "name"=>"caption",
                "required" => true,
                "filters"=> [['name' => 'StringTrim']], ]);
            $inputFilter->add([
                "name"=>"menuOrder",
                "required" => false, ]);
            $inputFilter->add([
                "name"=>"advanced",
                "required" => false, ]);
            $inputFilter->add([
                "name"=>"controller",
                "required" => true,
                "filters"=> [['name' => 'StringTrim']], ]);
            $inputFilter->add([
                "name"=>"action",
                "required" => false,
                "filters"=> [['name' => 'StringTrim']], ]);
            $inputFilter->add([
                "name"=>"class",
                "required" => false,
                "filters"=> [['name' => 'StringTrim']], ]);
            $inputFilter->add([
                "name"=>"description",
                "required" => false, ]);
            $inputFilter->add([
                "name"=>"parent",
                "required" => false, ]);
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
        $clone->setCaption($this->caption);
        $clone->setMenuOrder($this->menuOrder);
        $clone->setAdvanced($this->advanced);
        $clone->setController($this->controller);
        $clone->setAction($this->action);
        $clone->setClass($this->class);
        $clone->setDescription($this->description);
        $clone->setParent($this->parent);
        return $clone;
    }

    /**
     * toString method
     */
    public function toString()
    {
        return $this->caption;
    }
}
