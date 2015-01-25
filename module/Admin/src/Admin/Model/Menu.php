<?php
namespace Admin\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\ServiceManager\ServiceManager;

class Menu implements InputFilterAwareInterface
{
    protected $_inputFilter;

    /**
     * ServiceManager is a dependency injection we use for any additional methods requiring DB access.
     * Please, note that this is not the best way, but it does the job.
     *
     * @var $_serviceManager ServiceManager 
     */
    private $_serviceManager = null;

    /**
     * @var Int $_id
     * @return int
     */
    private $_id = 0;

    /**
     * @var null $_caption
     * @return string
     */
    private $_caption = null;

    /**
     * @var Int $_menuOrder
     * @return int
     */
    private $_menuOrder = 0;

    /**
     * @var null $_language
     * @return int
     */
    private $_language = null;

    /**
     * @var Int $_parent
     * @return int
     */
    private $_parent = 0;

    /**
     * @var null $_keywords
     * @return string
     */
    private $_keywords = null;

    /**
     * @var null $_description
     * @return string
     */
    private $_description = null;

    /**
     * @var Int $_menutype
     * @return int
     */
    private $_menutype = 0;

    /**
     * @var Int $_footercolumn
     * @return int
     */
    private $_footercolumn = 0;

    /**
     * @var null $_id
     * @return string
     */
    private $_menulink = null;
    
    /**
     * @param null $sm
     * @return ServiceManager
     */
    public function setServiceManager($sm = null)
    {
        if ($sm instanceof ServiceManager || $sm === null)
        {
            $this->_serviceManager = $sm;
        }
    }

    /**
     * @var array $data
     * @return mixed
     */
    public function exchangeArray(array $data)
    {
        $this->_id = (isset($data['id'])) ? $data['id'] : null;
        $this->_caption = (isset($data['caption'])) ? $data['caption'] : null;
        $this->_menuOrder = (isset($data['menuOrder'])) ? $data['menuOrder'] : null;
        $this->_language = (isset($data['language'])) ? $data['language'] : null;
        $this->_parent = (isset($data['parent'])) ? $data['parent'] : null;
        $this->_keywords = (isset($data['keywords'])) ? $data['keywords'] : null;
        $this->_description = (isset($data['description'])) ? $data['description'] : null;
        $this->_menutype = (isset($data['menutype'])) ? $data['menutype'] : null;
        $this->_footercolumn = (isset($data['footercolumn'])) ? $data['footercolumn'] : null;
        $this->_menulink = (isset($data['menulink'])) ? $data['menulink'] : null;
    }

    /**
     * constructor
     *
     * @param array $options
     * @param ServiceManager|null $sm
     */
    public function __construct(array $options, $sm = null)
    {
        $this->exchangeArray($options);
        if ($sm instanceof ServiceManager || $sm === null)
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
    public function setId($id = 0)
    {
        $this->_id = $id;
    }
    
    
    /**
    * Set caption
    * @param String $caption 
    */
    public function setCaption($caption = null)
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
    public function setMenuOrder($menuOrder = 1)
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
    * Set Language
    * @param int $ 
    */
    public function setLanguage($language = 1)
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
    * Get the related object from the DB
    */
    public function getLanguageObject()
    {
        try
        {
            return $this->serviceManager->get('LanguageTable')->getLanguage($this->_language);
        }
        catch (\Exception $e)
        {
            return $e->getMessage();
        }
    }
    
    /**
    * Set parent
    * @param int $parent 
    */
    public function setParent($parent = 0)
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
        try
        {
            return $this->serviceManager->get('MenuTable')->getMenu($this->_parent);
        }
        catch (\Exception $e)
        {
            return $e->getMessage();
        }
    }
    
    /**
     * Set keywords
     * @param String $keywords 
     */
    public function setKeywords($keywords = null)
    {
        $this->_keywords = $keywords;
    }

    /**
     * Get keywords
     * @return String
     */
    public function getKeywords()
    {
        return $this->_keywords;
    }
     
    /**
     * Set description
     * @param null $description 
     */
    public function setDescription($description = null)
    {
        $this->_description = $description;
    }

    /**
     * Get description
     * @return String
     */
    public function getDescription()
    {
        return $this->_description;
    }

    /**
     * Set menutype
     * @param Int $menutype 
     */
    public function setMenuType($menutype = 0)
    {
        $this->_menutype = $menutype;
    }

    /**
     * Get menutype
     * @return Int
     */
    public function getMenuType()
    {
        return $this->_menutype;
    }

    /**
     * Set footercolumn
     * @param Int $footercolumn 
     */
    public function setFooterColumn($footercolumn = 0)
    {
        $this->_footercolumn = $footercolumn;
    }

    /**
     * Get footercolumn
     * @return Int
     */
    public function getFooterColumn()
    {
        return $this->_footercolumn;
    }

    /**
     * Set menulink
     * @param null $menulink 
     */
    public function setMenuLink($menulink = null)
    {
        $this->_menulink = $menulink;
    }

    /**
     * Get menulink
     * @return Int
     */
    public function getMenuLink()
    {
        return $this->_menulink;
    }

    /**
     * Get menutype name
     * @return string
     */
    public function getMenuTypeAsName()
    {
        if ($this->getMenuType() === 0)
            return "Main menu";
        else if ($this->getMenuType() === 1)
            return "Left menu";
        else if ($this->getMenuType() === 3)
            return "Footer menu";
        else
            return "Right menu";
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
        foreach($data as $key => $value)
        {
            if (!in_array($key, $skip))
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
    public function getProperties(array $skip, $toJson = false)
    {
        $skip[] = "_serviceManager";
        $returnValue = array();
        $data = get_class_vars(get_class($this));
        foreach($data as $key => $value)
        {
            if (!in_array($key, $skip))
            {
                $returnValue[$key] = $this->$key;
            }
        }
        if ($toJson)
        {
            return \Zend\Json\Json::encode($returnValue);
        }
        return $returnValue;
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
                // 'validators' => array(
                //     array(
                //         'name' => 'Regex',
                //         'options' => array(
                //             'pattern' => '[0-9]',
                //             'messages' => array(
                //                 'Invalid input, only 0-9 characters allowed'
                //             ),
                //         ),
                //     ),
                // ),
            ));
            $inputFilter->add(array(
                "name"=>"caption",
                "required" => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array('name' => 'NotEmpty'),
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                        ),
                    ),
                ),
            ));
            $inputFilter->add(array(
                "name"=>"menuOrder",
                "required" => false,
                'filters'  => array(
                    array('name' => 'Int'),
                ),
                // 'validators' => array(
                //     array(
                //         'name' => 'Regex',
                //         'options' => array(
                //             'pattern' => '[0-9]',
                //             'messages' => array(
                //                 'Invalid input, only 0-9 characters allowed'
                //             ),
                //         ),
                //     ),
                // ),
            ));
            $inputFilter->add(array(
                "name"=>"language",
                "required" => false,
                'filters'  => array(
                    array('name' => 'Int'),
                ),
                // 'validators' => array(
                //     array(
                //         'name' => 'Regex',
                //         'options' => array(
                //             'pattern' => '[0-9]',
                //             'messages' => array(
                //                 'Invalid input, only 0-9 characters allowed'
                //             ),
                //         ),
                //     ),
                // ),
            ));
            $inputFilter->add(array(
                "name"=>"parent",
                "required" => false,
                'filters'  => array(
                    array('name' => 'Int'),
                ),
                // 'validators' => array(
                //     array(
                //         'name' => 'Regex',
                //         'options' => array(
                //             'pattern' => '[0-9]',
                //             'messages' => array(
                //                 'Invalid input, only 0-9 characters allowed'
                //             ),
                //         ),
                //     ),
                // ),
            ));
            $inputFilter->add(array(
                "name"=>"keywords",
                "required" => false,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                        ),
                    ),
                ),
            ));
            $inputFilter->add(array(
                "name"=>"description",
                "required" => false,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 0,
                            'max' => 150,
                        ),
                    ),
                ),
            ));
            $inputFilter->add(array(
                "name"=>"menutype",
                "required" => false,
                'filters'  => array(
                    array('name' => 'Int'),
                ),
                // 'validators' => array(
                //     array(
                //         'name' => 'Regex',
                //         'options' => array(
                //             'pattern' => '[0-9]',
                //             'messages' => array(
                //                 'Invalid input, only 0-9 characters allowed'
                //             ),
                //         ),
                //     ),
                // ),
            ));

            $inputFilter->add(array(
                "name"=>"footercolumn",
                "required" => false,
                'filters'  => array(
                    array('name' => 'Int'),
                ),
                // 'validators' => array(
                //     array(
                //         'name' => 'Regex',
                //         'options' => array(
                //             'pattern' => '[0-9]',
                //             'messages' => array(
                //                 'Invalid input, only 0-9 characters allowed'
                //             ),
                //         ),
                //     ),
                // ),
            ));
             $inputFilter->add(array(
                "name"=>"menulink",
                "required" => false,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                // 'validators' => array(
                //     array(
                //         'name' => 'Regex',
                //         'options' => array(
                //             'pattern' => '[0-9]',
                //             'messages' => array(
                //                 'Invalid input, only 0-9 characters allowed'
                //             ),
                //         ),
                //     ),
                // ),
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
        $clone->setCaption($this->_caption);
        $clone->setMenuOrder($this->_menuOrder);
        $clone->setLanguage($this->_language);
        $clone->setParent($this->_parent);
        $clone->setKeywords($this->_keywords);
        $clone->setDescription($this->_description);
        $clone->setMenuType($this->_menutype);
        $clone->setFooterColumn($this->_footercolumn);
        $clone->setMenuLink($this->_menulink);
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

?>