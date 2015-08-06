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
 * @author     Stanimir Dimitrov <stanimirdim92@gmail.com>
 * @copyright  2015 (c) Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 * @version    0.0.4
 * @link       TBA
 */

namespace Admin\Model;

class Menu
{
    /**
     * @var Int $id
     */
    private $id = 0;

    /**
     * @var null $caption
     */
    private $caption = null;

    /**
     * @var Int $menuOrder
     */
    private $menuOrder = 0;

    /**
     * @var null $language
     */
    private $language = 1;

    /**
     * @var Int $parent
     */
    private $parent = 0;

    /**
     * @var null $keywords
     */
    private $keywords = null;

    /**
     * @var null $description
     */
    private $description = null;

    /**
     * @var Int $menutype
     */
    private $menutype = 0;

    /**
     * @var Int $footercolumn
     */
    private $footercolumn = 0;

    /**
     * @var null $id
     */
    private $menulink = null;

    /**
     * @var array $data
     * @return mixed
     */
    public function exchangeArray(array $data = [])
    {
        $this->id = (isset($data['id'])) ? $data['id'] : $this->getId();
        $this->caption = (isset($data['caption'])) ? $data['caption'] : $this->getCaption();
        $this->menuOrder = (isset($data['menuOrder'])) ? $data['menuOrder'] : $this->getMenuOrder();
        $this->language = (isset($data['language'])) ? $data['language'] : $this->getLanguage();
        $this->parent = (isset($data['parent'])) ? $data['parent'] : $this->getParent();
        $this->keywords = (isset($data['keywords'])) ? $data['keywords'] : $this->getKeywords();
        $this->description = (isset($data['description'])) ? $data['description'] : $this->getDescription();
        $this->menutype = (isset($data['menutype'])) ? $data['menutype'] : $this->getMenuType();
        $this->footercolumn = (isset($data['footercolumn'])) ? $data['footercolumn'] : $this->getFooterColumn();
        $this->menulink = (isset($data['menulink'])) ? $data['menulink'] : $this->getMenuLink();
    }

    /**
     * Used into form binding
     */
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

    /**
     * constructor
     *
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $this->exchangeArray($options);
    }

    /**
     * Get id
     * @return int
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
    public function setMenuOrder($menuOrder = 0)
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
        return (property_exists($this, $property) ? $this->{$property} = $value : null);
    }

    /**
     * magic property exists (isset) checker
     */
    public function __isset($property)
    {
        return (property_exists($this, $property) ? isset($this->{$property}) : null);
    }

    /**
     * Serialize object or return it as an array
     *
     * @param  array $skip Remove the unnecessary objects from the array
     * @param  bool $serializable Should the function return a serialized object
     * @return array|string
     */
    public function getProperties(array $skip = [], $serializable = false)
    {
        $returnValue = [];
        $data = get_class_vars(get_class($this));
        foreach ($data as $key => $value) {
            if (!in_array($key, $skip)) {
                $returnValue[$key] = $this->{$key};
            }
        }
        if ((bool) $serializable === true) {
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
        $clone->setCaption($this->getCaption());
        $clone->setMenuOrder($this->getMenuOrder());
        $clone->setLanguage($this->getLanguage());
        $clone->setParent($this->getParent());
        $clone->setKeywords($this->getKeywords());
        $clone->setDescription($this->getDescription());
        $clone->setMenuType($this->getMenuType());
        $clone->setFooterColumn($this->getFooterColumn());
        $clone->setMenuLink($this->getMenuLink());
        return $clone;
    }
}
