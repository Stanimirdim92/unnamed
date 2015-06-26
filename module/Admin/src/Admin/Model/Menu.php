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
 * @category   Admin\Menu
 * @package    Unnamed
 * @author     Stanimir Dimitrov <stanimirdim92@gmail.com>
 * @copyright  2015 Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 * @version    0.0.3
 * @link       TBA
 */

namespace Admin\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\ServiceManager\ServiceManager;

class Menu implements InputFilterAwareInterface
{
    /**
     * @var null $inputFilter inputFilter
     */
    private $inputFilter = null;

    /**
     * @var null $serviceManager ServiceManager
     */
    private $serviceManager = null;

    /**
     * @var Int $id
     * @return int
     */
    private $id = 0;

    /**
     * @var null $caption
     * @return string
     */
    private $caption = null;

    /**
     * @var Int $menuOrder
     * @return int
     */
    private $menuOrder = 0;

    /**
     * @var null $language
     * @return int
     */
    private $language = 1;

    /**
     * @var Int $parent
     * @return int
     */
    private $parent = 0;

    /**
     * @var null $keywords
     * @return string
     */
    private $keywords = null;

    /**
     * @var null $description
     * @return string
     */
    private $description = null;

    /**
     * @var Int $menutype
     * @return int
     */
    private $menutype = 0;

    /**
     * @var Int $footercolumn
     * @return int
     */
    private $footercolumn = 0;

    /**
     * @var null $id
     * @return string
     */
    private $menulink = null;

    /**
     * @param null $sm ServiceManager
     * @return ServiceManager|null
     */
    public function setServiceLocator(ServiceManager $sm = null)
    {
        $this->serviceManager = $sm;
    }

    /**
     * @var array $data
     * @return mixed
     */
    public function exchangeArray(array $data = [])
    {
        $this->id = (isset($data['id'])) ? $data['id'] : $this->id;
        $this->caption = (isset($data['caption'])) ? $data['caption'] : $this->caption;
        $this->menuOrder = (isset($data['menuOrder'])) ? $data['menuOrder'] : $this->menuOrder;
        $this->language = (isset($data['language'])) ? $data['language'] : $this->language;
        $this->parent = (isset($data['parent'])) ? $data['parent'] : $this->parent;
        $this->keywords = (isset($data['keywords'])) ? $data['keywords'] : $this->keywords;
        $this->description = (isset($data['description'])) ? $data['description'] : $this->description;
        $this->menutype = (isset($data['menutype'])) ? $data['menutype'] : $this->menutype;
        $this->footercolumn = (isset($data['footercolumn'])) ? $data['footercolumn'] : $this->footercolumn;
        $this->menulink = (isset($data['menulink'])) ? $data['menulink'] : $this->menulink;
    }

    /**
     * constructor
     *
     * @param array $options
     * @param ServiceManager|null $sm
     */
    public function __construct(array $options = [], ServiceManager $sm = null)
    {
        $this->exchangeArray($options);
        $this->serviceManager = $sm;
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
    public function setId($id = 0)
    {
        $this->id = $id;
    }


    /**
     * Set caption
     * @param String $caption
     */
    public function setCaption($caption = null)
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
    public function setMenuOrder($menuOrder = 1)
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
     * Set Language
     * @param int $
     */
    public function setLanguage($language = 1)
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
     * Get the related object from the DB
     */
    public function getLanguageObject()
    {
        try {
            return $this->serviceManager->get('LanguageTable')->getLanguage($this->language);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * Set parent
     * @param int $parent
     */
    public function setParent($parent = 0)
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
            return $this->serviceManager->get('MenuTable')->getMenu($this->parent, $this->language);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * Set keywords
     * @param String $keywords
     */
    public function setKeywords($keywords = null)
    {
        $this->keywords = $keywords;
    }

    /**
     * Get keywords
     * @return String
     */
    public function getKeywords()
    {
        return $this->keywords;
    }

    /**
     * Set description
     * @param null $description
     */
    public function setDescription($description = null)
    {
        $this->description = $description;
    }

    /**
     * Get description
     * @return String
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set menutype
     * @param Int $menutype
     */
    public function setMenuType($menutype = 0)
    {
        $this->menutype = $menutype;
    }

    /**
     * Get menutype
     * @return Int
     */
    public function getMenuType()
    {
        return $this->menutype;
    }

    /**
     * Set footercolumn
     * @param Int $footercolumn
     */
    public function setFooterColumn($footercolumn = 0)
    {
        $this->footercolumn = $footercolumn;
    }

    /**
     * Get footercolumn
     * @return Int
     */
    public function getFooterColumn()
    {
        return $this->footercolumn;
    }

    /**
     * Set menulink
     * @param null $menulink
     */
    public function setMenuLink($menulink = null)
    {
        $this->menulink = $menulink;
    }

    /**
     * Get menulink
     * @return Int
     */
    public function getMenuLink()
    {
        return $this->menulink;
    }

    /**
     * Get menutype name
     * @return string
     */
    public function getMenuTypeAsName()
    {
        if ($this->getMenuType() === 0) {
            return "Main menu";
        } elseif ($this->getMenuType() === 1) {
            return "Left menu";
        } elseif ($this->getMenuType() === 3) {
            return "Footer menu";
        } else {
            return "Right menu";
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
        (property_exists($this, $property) ? $this->{$property} = $value : null);
    }

    /**
     * magic property exists (isset) checker
     */
    public function __isset($property)
    {
        return (property_exists($this, $property) ? isset($this->{$property}) : null);
    }

    /**
     * magic serializer
     */
    public function __sleep()
    {
        $skip = ["serviceManager"];
        $returnValue = [];
        $data = get_class_vars(get_class($this));
        foreach ($data as $key => $value) {
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
    public function getProperties(array $skip = [], $serializable = false)
    {
        $returnValue = [];
        $data = get_class_vars(get_class($this));
        foreach ($data as $key => $value) {
            if (!in_array($key, $skip)) {
                $returnValue[$key] = $this->$key;
            }
        }
        if ((bool) $serializable === true) {
            return serialize($returnValue);
        }
        return $returnValue;
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
                "name"=>"caption",
                "required" => true,
                'filters' => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                ],
                'validators' => [
                    ['name' => 'NotEmpty'],
                    [
                        'name'    => 'StringLength',
                        'options' => [
                            'encoding' => 'UTF-8',
                            'min' => 1,
                            'max' => 200,
                        ],
                    ],
                ],
            ]);
            $inputFilter->add([
                "name"=>"menuOrder",
                "required" => false,
                'filters'  => [
                    ['name' => 'Int'],
                ],
                'validators' => [
                    [
                        'name' => 'Regex',
                        'options' => [
                            'pattern' => '/^[0-9]+$/',
                        ],
                    ],
                ],
            ]);
            $inputFilter->add([
                "name"=>"language",
                "required" => false,
                'filters'  => [
                    ['name' => 'Int'],
                ],
                'validators' => [
                    [
                        'name' => 'Regex',
                        'options' => [
                            'pattern' => '/^[0-9]+$/',
                        ],
                    ],
                ],
            ]);
            $inputFilter->add([
                "name"=>"parent",
                "required" => false,
                'filters'  => [
                    ['name' => 'Int'],
                ],
                'validators' => [
                    [
                        'name' => 'Regex',
                        'options' => [
                            'pattern' => '/^[0-9]+$/',
                        ],
                    ],
                ],
            ]);
            $inputFilter->add([
                "name"=>"keywords",
                "required" => false,
                'filters' => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                ],
                'validators' => [
                    [
                        'name'    => 'StringLength',
                        'options' => [
                            'encoding' => 'UTF-8',
                            'min' => 0,
                            'max' => 200,
                        ],
                    ],
                ],
            ]);
            $inputFilter->add([
                "name"=>"description",
                "required" => false,
                'filters' => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                ],
                'validators' => [
                    [
                        'name'    => 'StringLength',
                        'options' => [
                            'encoding' => 'UTF-8',
                            'min' => 0,
                            'max' => 150,
                        ],
                    ],
                ],
            ]);
            $inputFilter->add([
                "name"=>"menutype",
                "required" => false,
                'filters'  => [
                    ['name' => 'Int'],
                ],
                'validators' => [
                    [
                        'name' => 'Regex',
                        'options' => [
                            'pattern' => '/^[0-9]+$/',
                        ],
                    ],
                ],
            ]);

            $inputFilter->add([
                "name"=>"footercolumn",
                "required" => false,
                'filters'  => [
                    ['name' => 'Int'],
                ],
                'validators' => [
                    [
                        'name' => 'Regex',
                        'options' => [
                            'pattern' => '/^[0-9]+$/',
                        ],
                    ],
                ],
            ]);
            $inputFilter->add([
                "name"=>"menulink",
                "required" => false,
                'filters' => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                    ['name' => 'StringToLower'],
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
        $clone->setCaption($this->caption);
        $clone->setMenuOrder($this->menuOrder);
        $clone->setLanguage($this->language);
        $clone->setParent($this->parent);
        $clone->setKeywords($this->keywords);
        $clone->setDescription($this->description);
        $clone->setMenuType($this->menutype);
        $clone->setFooterColumn($this->footercolumn);
        $clone->setMenuLink($this->menulink);
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
