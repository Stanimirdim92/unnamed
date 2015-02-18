<?php
/**
 * MIT License
 * ===========
 *
 * Copyright (c) 2015 Stanimir Dimitrov <stanimirdim92@gmail.com>
 *
 * Permission is hereby granted, free of charge, to any person obtaining
 * a copy of this software and associated documentation files (the
 * "Software"), to deal in the Software without restriction, including
 * without limitation the rights to use, copy, modify, merge, publish,
 * distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to
 * the following conditions:
 *
 * The above copyright notice and this permission notice shall be included
 * in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS
 * OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
 * IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY
 * CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT,
 * TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE
 * SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 *
 * @category   Admin\Content
 * @package    ZendPress
 * @author     Stanimir Dimitrov <stanimirdim92@gmail.com>
 * @copyright  2015 Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 * @version    0.03
 * @link       TBA
 */

namespace Admin\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\ServiceManager\ServiceManager;

class Content implements InputFilterAwareInterface
{
    /**
     * @var null $_inputFilter inputFilter
     */
    protected $_inputFilter = null;

    /**
     * @var null $_serviceManager ServiceManager 
     */
    private $_serviceManager = null;

    /**
     * @var Int $_id
     * @return int
     */
    private $_id = 0;

    /**
     * @var Int $_id
     * @return int
     */
    private $_menu = 0;

    /**
     * @var null $_title
     * @return string
     */
    private $_title = null;

    /**
     * @var null $_preview
     * @return string
     */
    private $_preview = null;

    /**
     * @var null $_text
     * @return string
     */
    private $_text = null;

    /**
     * @var Int $_id
     * @return int
     */
    private $_menuOrder = 0;

    /**
     * @var Int $_id
     * @return int
     */
    private $_type = 0;

    /**
     * @var null $_date
     * @return string
     */
    private $_date = "0000-00-00 00:00:00";

    /**
     * @var Int $_language
     * @return int
     */
    private $_language = 1;

    /**
     * @var null $_titleLink
     * @return string
     */
    private $_titleLink = null;
    
    /**
     * @param null $sm ServiceManager
     * @return ServiceManager|null
     */
    public function setServiceManager(ServiceManager $sm = null)
    {
        $this->_serviceManager = $sm;
    }

    /**
     * @var array $data
     * @return mixed
     */
    public function exchangeArray(array $data = array())
    {
        $this->_id = (isset($data['id'])) ? $data['id'] : 0;
        $this->_menu = (isset($data['menu'])) ? $data['menu'] : 0;
        $this->_title = (isset($data['title'])) ? $data['title'] : null;
        $this->_preview = (isset($data['preview'])) ? $data['preview'] : null;
        $this->_text = (isset($data['text'])) ? $data['text'] : null;
        $this->_menuOrder = (isset($data['menuOrder'])) ? $data['menuOrder'] : 0;
        $this->_type = (isset($data['type'])) ? $data['type'] : 0;
        $this->_date = (isset($data['date'])) ? $data['date'] : "0000-00-00 00:00:00";
        $this->_language = (isset($data['language'])) ? $data['language'] : 1;
        $this->_titleLink = (isset($data['titleLink'])) ? $data['titleLink'] : null;
    }

    /**
     * constructor
     * 
     * @param array $options
     * @param ServiceManager|null $sm
     */
    public function __construct(array $options = array(), ServiceManager $sm = null)
    {
        $this->exchangeArray($options);
        $this->_serviceManager = $sm;
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
    * Set Menu
    * @param int $menu
    */
    public function setMenu($menu = 0)
    {
        $this->_menu = $menu;
    }

    /**
    * Get menu
    * @return int
    */
    public function getMenu()
    {
        return $this->_menu;
    }
 
    /**
    * Get the related object from the DB
    */
    public function getMenuObject()
    {
        try
        {
            return $this->serviceManager->get('MenuTable')->getMenu($this->_menu);
        }
        catch (\Exception $e)
        {
            return $e->getMessage();
        }
    }
    
    /**
    * Set title
    * @param null $title 
    */
    public function setTitle($title = null)
    {
        $this->_title = $title;
    }

    /**
    * Get title
    * @return String
    */
    public function getTitle()
    {
        return $this->_title;
    }

    /**
    * Set titleLink
    * @param null $titleLink 
    */
    public function setTitleLink($titleLink = null)
    {
        $this->_titleLink = $titleLink;
    }

    /**
    * Get titleLink
    * @return String
    */
    public function getTitleLink()
    {
        return $this->_titleLink;
    }
     
    /**
    * Set preview
    * @param String $preview 
    */
    public function setPreview($preview = null)
    {
        $this->_preview = $preview;
    }

    /**
    * Get preview
    * @return String
    */
    public function getPreview()
    {
        return $this->_preview;
    }
     
    /**
    * Set text
    * @param String $text 
    */
    public function setText($text = null)
    {
        $this->_text = $text;
    }

    /**
    * Get text
    * @return String
    */
    public function getText()
    {
        return $this->_text;
    }
     
    /**
    * Set order
    * @param int $menuOrder 
    */
    public function setMenuOrder($menuOrder = 0)
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
    * Set type
    * @param int $type 
    */
    public function setType($type = 0)
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
     
    /**
    * Set date
    * @param String $date 
    */
    public function setDate($date = "0000-00-00 00:00:00")
    {
        $this->_date = $date;
    }

    /**
    * Get date
    * @return String
    */
    public function getDate()
    {
        return $this->_date;
    }
     
    /**
    * Set Language
    * @param int $language
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
        (property_exists($this, '_'. $property) ? $this->{'_'. $property} = $value : null);
    }

    /**
     * magic property exists (isset) checker
     */
    public function __isset($property)
    {
        return (property_exists($this, '_'. $property) ? isset($this->{'_'. $property}) : null);
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
    public function getProperties(array $skip = array(), $serializable = false)
    {
        $returnValue = array();
        $data = get_class_vars(get_class($this));
        foreach($data as $key => $value)
        {
            if (!in_array($key, $skip))
            {
                $returnValue[$key] = $this->$key;
            }
        }
        if ($serializable)
        {
            return serialize($returnValue);
        }
        return $returnValue;
    }
   
    /**
     * This method is a copy constructor that will return a copy object (except for the id field)
     * Note that this method will not save the object
     */
    public function getCopy()
    {
        $clone = new self();
        $clone->setMenu($this->_menu);
        $clone->setTitle($this->_title);
        $clone->setPreview($this->_preview);
        $clone->setText($this->_text);
        $clone->setMenuOrder($this->_menuOrder);
        $clone->setType($this->_type);
        $clone->setDate($this->_date);
        $clone->setLanguage($this->_language);
        $clone->setTitleLink($this->_titleLink);
        return $clone;
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
                "name"=>"preview",
                "required" => false,
                'validators' => array(
                    array(
                        'name' => 'Zend\Validator\File\Size',
                        'options' => array(
                            'min' => 0,
                            'max' => 3145728, //3mb
                            'useByteString' => true,
                        )
                    ),
                    array(
                        'name' => 'Zend\Validator\File\Extension',
                        'options' => array(
                            'extension' => array(
                                'jpg',
                                'gif',
                                'png',
                                'jpeg',
                                'bmp',
                                'webp',
                            ), 
                            'case' => true,
                        ),
                    ),
                ),
            ));
            $inputFilter->add(array(
                "name"=>"title",
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
                            'min' => 1,
                            'max' => 200,
                        ),
                    ),
                ),
            ));
            $inputFilter->add(array(
                "name"=>"text",
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
                            'min' => 1,
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
                'validators' => array(
                    array(
                        'name' => 'Regex',
                        'options' => array(
                            'pattern' => '/^[0-9]+$/',
                        ),
                    ),
                ),
            ));
            $inputFilter->add(array(
                "name"=>"language",
                "required" => false,
                'filters'  => array(
                    array('name' => 'Int'),
                ),
                'validators' => array(
                    array(
                        'name' => 'Regex',
                        'options' => array(
                            'pattern' => '/^[0-9]+$/',
                        ),
                    ),
                ),
            ));
            $inputFilter->add(array(
                "name"=>"menu",
                "required" => false,
                'filters'  => array(
                    array('name' => 'Int'),
                ),
                'validators' => array(
                    array(
                        'name' => 'Regex',
                        'options' => array(
                            'pattern' => '/^[0-9]+$/',
                        ),
                    ),
                ),
            ));
            $inputFilter->add(array(
                "name"=>"type",
                "required" => false,
                'filters'  => array(
                    array('name' => 'Int'),
                ),
                'validators' => array(
                    array(
                        'name' => 'Regex',
                        'options' => array(
                            'pattern' => '/^[0-1]+$/',
                        ),
                    ),
                ),
            ));
            $inputFilter->add(array(
                "name"=>"date",
                "required" => false,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'date',
                        'options' => array(
                            'locale' => 'en',
                            'format' => 'Y-m-d H:i:s'
                        ),
                    ),
                ),
            ));
            $inputFilter->add(array(
                "name"=>"titleLink",
                "required" => false,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                    array('name' => 'StringToLower'),
                ),
            ));
            $this->_inputFilter = $inputFilter;
        }
        return $this->_inputFilter;
    }

    /**
     * toString method
     */
    public function toString()
    {
        return $this->_title;
    }

}
