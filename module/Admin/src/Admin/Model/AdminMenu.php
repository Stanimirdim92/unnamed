<?php
namespace Admin\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\ServiceManager\ServiceManager;

class AdminMenu implements InputFilterAwareInterface
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
     * @param String $_caption
     * @return string
     */
    private $_caption;

    /**
     * @param Int $_menuOrder
     * @return int
     */
    private $_menuOrder;

    /**
     * @param Int $_advanced
     * @return int
     */
    private $_advanced;

    /**
     * @param String $_controller
     * @return string
     */
    private $_controller;

    /**
     * @param String $_action
     * @return string
     */
    private $_action;

    /**
     * @param String $_class
     * @return string
     */
    private $_class;

    /**
     * @param String $_description
     * @return string
     */
    private $_description;

    /**
     * @param Int $_parent
     * @return int
     */
    private $_parent;
    
    public function setServiceManager($sm)
    {
        $this->_serviceManager = $sm;
    }

    public function exchangeArray($data)
    {
        $this->_id = (isset($data['id'])) ? $data['id'] : null;
        $this->_caption = (isset($data['caption'])) ? $data['caption'] : null;
        $this->_menuOrder = (isset($data['menuOrder'])) ? $data['menuOrder'] : null;
        $this->_advanced = (isset($data['advanced'])) ? $data['advanced'] : null;
        $this->_controller = (isset($data['controller'])) ? $data['controller'] : null;
        $this->_action = (isset($data['action'])) ? $data['action'] : null;
        $this->_class = (isset($data['class'])) ? $data['class'] : null;
        $this->_description = (isset($data['description'])) ? $data['description'] : null;
        $this->_parent = (isset($data['parent'])) ? $data['parent'] : null;
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
     * Set caption
     * @param String $caption
     */
    public function setCaption($caption)
    {
        $this->_caption = $caption;
    }

    /**
     * Get caption
     * @return String
     */
    public function getCaption()
    {
        return $this->_caption;
    }

    /**
     * Set menuOrder
     * @param int $menuOrder
     */
    public function setMenuOrder($menuOrder)
    {
        $this->_menuOrder = $menuOrder;
    }

    /**
     * Get menuOrder
     * @return int
     */
    public function getMenuOrder()
    {
        return $this->_menuOrder;
    }
     
    /**
     * Set advanced
     * @param Boolean $advanced
     */
    public function setAdvanced($advanced)
    {
        $this->_advanced = $advanced;
    }

    /**
     * Get advanced
     * @return Boolean
     */
    public function getAdvanced()
    {
        return $this->_advanced;
    }
     
    /**
     * Set controller
     * @param String $controller
     */
    public function setController($controller)
    {
        $this->_controller = $controller;
    }

    /**
     * Get controller
     * @return String
     */
    public function getController()
    {
        return $this->_controller;
    }
     
    /**
     * Set action
     * @param String $action
     */
    public function setAction($action)
    {
        $this->_action = $action;
    }

    /**
     * Get action
     * @return String
     */
    public function getAction()
    {
        return $this->_action;
    }

    /**
     * Set class
     * @param String $class
     */
    public function setClass($class)
    {
        $this->_class = $class;
    }

    /**
     * Get class
     * @return String
     */
    public function getClass()
    {
        return $this->_class;
    }

    /**
     * Set description
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->_description = $description;
    }

    /**
     * Get description
     * @return string
     */
    public function getDescription()
    {
        return $this->_description;
    }

    /**
     * Set parent
     * @param int $parent
     */
    public function setParent($parent)
    {
        $this->_parent = $parent;
    }

    /**
     * Get parent
     * @return int
     */
    public function getParent()
    {
        return $this->_parent;
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
                "name"=>"caption",
                "required" => true,
                "filters"=> array(array('name' => 'StringTrim')),));
            $inputFilter->add(array(
                "name"=>"menuOrder",
                "required" => false,));
            $inputFilter->add(array(
                "name"=>"advanced",
                "required" => false,));
            $inputFilter->add(array(
                "name"=>"controller",
                "required" => true,
                "filters"=> array(array('name' => 'StringTrim')),));
            $inputFilter->add(array(
                "name"=>"action",
                "required" => false,
                "filters"=> array(array('name' => 'StringTrim')),));
            $inputFilter->add(array(
                "name"=>"class",
                "required" => false,
                "filters"=> array(array('name' => 'StringTrim')),));
            $inputFilter->add(array(
                "name"=>"description",
                "required" => false,));
            $inputFilter->add(array(
                "name"=>"parent",
                "required" => false,));
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
        $clone->setCaption($this->_caption);
        $clone->setMenuOrder($this->_menuOrder);
        $clone->setAdvanced($this->_advanced);
        $clone->setController($this->_controller);
        $clone->setAction($this->_action);
        $clone->setClass($this->_class);
        $clone->setDescription($this->_description);
        $clone->setParent($this->_parent);
        return $clone;
    }

    /**
     * toString method
     */
    public function toString()
    {
        return $this->_caption;
    }
}
