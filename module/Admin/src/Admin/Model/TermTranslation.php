<?php
namespace Admin\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class TermTranslation implements InputFilterAwareInterface
{
    private $_inputFilter;
    /**
     * ServiceManager is a dependency injection we use for any additional methods requiring DB access
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
     * @param Int $_language
     * @return int
     */
    private $_language;

    /**
     * @param String $_translation
     * @return string
     */
    private $_translation;

    /**
     * @param Int $_term
     * @return int
     */
    private $_term;

    public function setServiceManager($sm)
    {
        $this->_serviceManager = $sm;
    }

    public function exchangeArray($data)
    {
        $this->_id = (isset($data['id'])) ? $data['id'] : null;
        $this->_language = (isset($data['language'])) ? $data['language'] : null;
        $this->_translation = (isset($data['translation'])) ? $data['translation'] : null;
        $this->_term = (isset($data['term'])) ? $data['term'] : null;

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
    * Set Language
    * @param int $ 
    */
    public function setLanguage($language)
    {
        $this->_language = (int) $language;
    }

    /**
    * Get language
    * @return null|int
    */
    public function getLanguage()
    {
        return $this->_language;
    }
     
    /**
    * Set translation
    * @param String $translation 
    */
    public function setTranslation($translation)
    {
        $this->_translation = (String) $translation;
    }

    /**
    * Get translation
    * @return null|String
    */
    public function getTranslation()
    {
        return $this->_translation;
    }
     
    /**
    * Set Term
    * @param int $ 
    */
    public function setTerm($term)
    {
        $this->_term = (int) $term;
    }

    public function getLanguageObject()
    {
        return $this->_serviceManager->get("LanguageTable")->getLanguage($this->language);
    }

    /**
    * Get term
    * @return null|int
    */
    public function getTerm()
    {
        return $this->_term;
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
        if (!$this->inputFilter) 
        {
            $inputFilter = new InputFilter();
            $inputFilter->add(array(
                'name' => 'id',
                'required' => false, 
                'filters' => array(
                    array('name' => 'Int')
                ),
            ));

            $inputFilter->add(array(
                'name' => 'language',
                'required' => false, 
                'filters' => array(
                    array('name' => 'Int')
                ),
            ));

           $inputFilter->add(array(
                "name"=>"translation",
                'required' => false, 
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                        ),
                    ),
                    array('name' => 'NotEmpty'),
                ),
            ));

           $inputFilter->add(array(
                'name' => 'term',
                'required' => false, 
                'filters' => array(
                    array('name' => 'Int')
                ),
            ));
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
        $clone->setLanguage($this->_language);
        $clone->setTranslation($this->_translation);
        $clone->setTerm($this->_term);
        return $clone;
    }

    /**
     * toString method
     */
    public function toString()
    {
        return $this->id;
    }
}
