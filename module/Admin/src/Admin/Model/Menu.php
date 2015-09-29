<?php

/**
 * @copyright  2015 (c) Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 *
 * @version    0.0.14
 *
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
     * @var bool $active
     */
    private $active = 1;

    /**
     * @param string $class
     */
    private $class = null;

    /**
     * @var array $data
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
        $this->active = (isset($data['active'])) ? $data['active'] : $this->getActive();
        $this->class = (isset($data['class'])) ? $data['class'] : $this->getClass();
    }

    /**
     * Used into form binding.
     */
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

    /**
     * constructor.
     *
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $this->exchangeArray($options);
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set id.
     *
     * @param int
     */
    public function setId($id = 0)
    {
        $this->id = $id;
    }


    /**
     * Set caption.
     *
     * @param String $caption
     */
    public function setCaption($caption = null)
    {
        $this->caption = $caption;
    }

    /**
     * Get caption.
     *
     * @return String
     */
    public function getCaption()
    {
        return $this->caption;
    }

    /**
     * Set menuOrder.
     *
     * @param int $menuOrder
     */
    public function setMenuOrder($menuOrder = 0)
    {
        $this->menuOrder = $menuOrder;
    }

    /**
     * Get menuOrder.
     *
     * @return int
     */
    public function getMenuOrder()
    {
        return $this->menuOrder;
    }

    /**
     * Set active.
     *
     * @param Boolean $active
     */
    public function setActive($active = 0)
    {
        $this->active = $active;
    }

    /**
     * Get active.
     *
     * @return Boolean
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Set Language.
     *
     * @param int $language
     */
    public function setLanguage($language = 1)
    {
        $this->language = $language;
    }

    /**
     * Get language.
     *
     * @return int
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * Set parent.
     *
     * @param int $parent
     */
    public function setParent($parent = 0)
    {
        $this->parent = $parent;
    }

    /**
     * Get parent.
     *
     * @return int
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Set keywords.
     *
     * @param String $keywords
     */
    public function setKeywords($keywords = null)
    {
        $this->keywords = $keywords;
    }

    /**
     * Get keywords.
     *
     * @return String
     */
    public function getKeywords()
    {
        return $this->keywords;
    }

    /**
     * Set description.
     *
     * @param null $description
     */
    public function setDescription($description = null)
    {
        $this->description = $description;
    }

    /**
     * Get description.
     *
     * @return String
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set menutype.
     *
     * @param Int $menutype
     */
    public function setMenuType($menutype = 0)
    {
        $this->menutype = $menutype;
    }

    /**
     * Get menutype.
     *
     * @return Int
     */
    public function getMenuType()
    {
        return $this->menutype;
    }

    /**
     * Set footercolumn.
     *
     * @param Int $footercolumn
     */
    public function setFooterColumn($footercolumn = 0)
    {
        $this->footercolumn = $footercolumn;
    }

    /**
     * Get footercolumn.
     *
     * @return Int
     */
    public function getFooterColumn()
    {
        return $this->footercolumn;
    }

    /**
     * Set menulink.
     *
     * @param null $menulink
     */
    public function setMenuLink($menulink = null)
    {
        $this->menulink = $menulink;
    }

    /**
     * Get menulink.
     *
     * @return Int
     */
    public function getMenuLink()
    {
        return $this->menulink;
    }

    /**
     * Set class.
     *
     * @param String $class
     */
    public function setClass($class = null)
    {
        $this->class = $class;
    }

    /**
     * Get class.
     *
     * @return String
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * Get menutype name.
     *
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
     * This method is a copy constructor that will return a copy object (except for the id field).
     * Note that this method will not save the object.
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
        $clone->setActive($this->getActive());
        $clone->setClass($this->getClass());
        return $clone;
    }
}
