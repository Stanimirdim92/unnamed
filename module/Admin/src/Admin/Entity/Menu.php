<?php

/**
 * @copyright  2015 (c) Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 *
 * @version    0.0.21
 *
 * @link       TBA
 */

namespace Admin\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Menu
 *
 * @ORM\Entity
 * @ORM\Table(name="menu")
 * @ORM\Entity(repositoryClass="Admin\Repository\MenuRepository")
 */
final class Menu
{
    /**
     * @var integer
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(name="id", type="integer", nullable=false)
     */
    private $id = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="caption", type="string", length=200, nullable=true)
     */
    private $caption;

    /**
     * @var integer
     *
     * @ORM\Column(name="menuOrder", type="integer", nullable=false)
     */
    private $menuOrder = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="language", type="smallint", nullable=false)
     */
    private $language = 1;

    /**
     * @var integer
     *
     * @ORM\Column(name="parent", type="integer", nullable=false)
     */
    private $parent = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="keywords", type="string", length=100, nullable=true)
     */
    private $keywords;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=150, nullable=true)
     */
    private $description;

    /**
     * @var integer
     *
     * @ORM\Column(name="menutype", type="smallint", nullable=false)
     */
    private $menutype = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="footercolumn", type="integer", nullable=false)
     */
    private $footercolumn = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="menulink", type="string", length=255, nullable=true)
     */
    private $menulink;

    /**
     * @var boolean
     *
     * @ORM\Column(name="active", type="smallint", nullable=false)
     */
    private $active = true;

    /**
     * @var string
     *
     * @ORM\Column(name="class", type="string", length=50, nullable=true)
     */
    private $class;

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
        $this->active = (isset($data['active'])) ? $data['active'] : $this->isActive();
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
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $this->exchangeArray($options);
    }

    /**
     * Get id.
     *
     * @return integer
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
    public function setCaption($caption)
    {
        $this->caption = $caption;
    }

    /**
     * Get caption.
     *
     * @return string
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
     * @return integer
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
    public function setActive($active = true)
    {
        $this->active = $active;
    }

    /**
     * Get active.
     *
     * @return Boolean
     */
    public function isActive()
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
     * @return integer
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
     * @return integer
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
    public function setKeywords($keywords)
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
    public function setDescription($description)
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
     * @return integer
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
     * @return integer
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
    public function setMenuLink($menulink)
    {
        $this->menulink = $menulink;
    }

    /**
     * Get menulink.
     *
     * @return integer
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
    public function setClass($class)
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
}
