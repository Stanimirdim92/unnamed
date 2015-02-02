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
     * @var null $_extract
     * @return string
     */
    private $_extract = null;

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
     * @var Int $_titleLink
     * @return int
     */
    private $_titleLink = null;
    
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
        $this->_menu = (isset($data['menu'])) ? $data['menu'] : null;
        $this->_title = (isset($data['title'])) ? $data['title'] : null;
        $this->_preview = (isset($data['preview'])) ? $data['preview'] : null;
        $this->_extract = (isset($data['extract'])) ? $data['extract'] : null;
        $this->_text = (isset($data['text'])) ? $data['text'] : null;
        $this->_menuOrder = (isset($data['menuOrder'])) ? $data['menuOrder'] : null;
        $this->_type = (isset($data['type'])) ? $data['type'] : null;
        $this->_date = (isset($data['date'])) ? $data['date'] : null;
        $this->_language = (isset($data['language'])) ? $data['language'] : null;
        $this->_titleLink = (isset($data['titleLink'])) ? $data['titleLink'] : null;
    }

    /**
     * constructor
     * 
     * @param array $options
     * @param ServiceManager|null $sm
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
    * Set extract
    * @param String $extract 
    */
    public function setExtract($extract = null)
    {
        $this->_extract = $extract;
    }

    /**
    * Get extract
    * @return String
    */
    public function getExtract()
    {
        return $this->_extract;
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
        $clone->setExtract($this->_extract);
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
        //TODO add inputfilter
    }

    /**
     * toString method
     */
    public function toString()
    {
        return $this->_title;
    }

}
