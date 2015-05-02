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
 * @category   Admin\Language
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

class Language implements InputFilterAwareInterface
{
    /**
     * @var null $_inputFilter inputFilter
     */
    private $_inputFilter = null;

    /**
     * @var null $_serviceManager ServiceManager
     */
    private $_serviceManager = null;

    /**
     * @param Int $_id
     * @return int
     */
    private $_id = 0;

    /**
     * @param null|string $_name
     * @return null|string
     */
    private $_name = null;

    /**
     * @param bool $_active
     * @return bool
     */
    private $_active = 0;

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
        $this->_id = (isset($data['id'])) ? $data['id'] : $this->_id;
        $this->_name = (isset($data['name'])) ? $data['name'] : $this->_name;
        $this->_active = (isset($data['active'])) ? $data['active'] : $this->_active;
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
     * Set name
     * @param null|string $name
     */
    public function setName($name = null)
    {
        $this->_name = $name;
    }

    /**
     * Get name
     * @return String
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * Set active
     * @param Boolean $active
     */
    public function setActive($active = 0)
    {
        $this->_active = $active;
    }

    /**
     * Get active
     * @return Boolean
     */
    public function getActive()
    {
        return $this->_active;
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
    public function getProperties(array $skip = array(), $serializable = false)
    {
        $returnValue = array();
        $data = get_class_vars(get_class($this));
        foreach ($data as $key => $value) {
            if (!in_array($key, $skip)) {
                $returnValue[$key] = $this->$key;
            }
        }
        if ($serializable) {
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
        if (!$this->_inputFilter) {
            $inputFilter = new InputFilter();
            $inputFilter->add(array(
                'name' => 'id',
                'required' => false,
                'filters' => array(
                    array('name' => 'Int'),
                ),
            ));
            $inputFilter->add(array(
                "name"=>"name",
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
                            'max' => 10,
                        ),
                    ),
                ),
            ));
            $inputFilter->add(array(
                "name"=>"active",
                "required" => false,
                'filters' => array(
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
        $copy = new self();
        $copy->setName($this->_name);
        $copy->setActive($this->_active);
        return $copy;
    }

    /**
     * toString method
     * @return String
     */
    public function toString()
    {
        return $this->_name;
    }
}
