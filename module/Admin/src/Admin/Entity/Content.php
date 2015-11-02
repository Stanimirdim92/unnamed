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
 * Content
 *
 * @ORM\Entity
 * @ORM\Table(name="content")
 */
final class Content
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
     * @var integer
     *
     * @ORM\Column(name="menu", type="integer", nullable=false)
     */
    private $menu = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=200, nullable=true)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="preview", type="string", length=100, nullable=true)
     */
    private $preview;

    /**
     * @var string
     *
     * @ORM\Column(name="text", type="text", nullable=true)
     */
    private $text;

    /**
     * @var integer
     *
     * @ORM\Column(name="menuOrder", type="integer", nullable=false)
     */
    private $menuOrder = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="type", type="smallint", nullable=false)
     */
    private $type = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="date", type="string", nullable=false)
     */
    private $date = "0000-00-00 00:00:00";

    /**
     * @var integer
     *
     * @ORM\Column(name="language", type="integer", nullable=false)
     */
    private $language = 1;

    /**
     * @var string
     *
     * @ORM\Column(name="titleLink", type="string", length=255, nullable=true)
     */
    private $titleLink;

    /**
     * @var boolean
     *
     * @ORM\Column(name="active", type="smallint", nullable=false)
     */
    private $active = 1;

    /**
     * @var string
     *
     * @ORM\Column(name="author", type="string", length=200, nullable=true)
     */
    private $author;

    /**
     * @param array $data
     */
    public function exchangeArray(array $data = [])
    {
        $this->id = (isset($data['id'])) ? $data['id'] : $this->getId();
        $this->menu = (isset($data['menu'])) ? $data['menu'] : $this->getMenu();
        $this->title = (isset($data['title'])) ? $data['title'] : $this->getTitle();
        $this->preview = (isset($data['preview'])) ? $data['preview'] : $this->getPreview();
        $this->text = (isset($data['text'])) ? $data['text'] : $this->getText();
        $this->menuOrder = (isset($data['menuOrder'])) ? $data['menuOrder'] : $this->getMenuOrder();
        $this->type = (isset($data['type'])) ? $data['type'] : $this->getType();
        $this->date = (isset($data['date'])) ? $data['date'] : $this->getDate();
        $this->language = (isset($data['language'])) ? $data['language'] : $this->getLanguage();
        $this->titleLink = (isset($data['titleLink'])) ? $data['titleLink'] :  $this->getTitleLink();
        $this->active = (isset($data['active'])) ? $data['active'] : $this->isActive();
        $this->author = (isset($data['author'])) ? $data['author'] : $this->getAuthor();
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
     * Set Menu.
     *
     * @param int $menu
     */
    public function setMenu($menu = 0)
    {
        $this->menu = $menu;
    }

    /**
     * Get menu.
     *
     * @return int
     */
    public function getMenu()
    {
        return $this->menu;
    }

    /**
     * Set title.
     *
     * @param null $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Get title.
     *
     * @return String
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set titleLink.
     *
     * @param null $titleLink
     */
    public function setTitleLink($titleLink)
    {
        $this->titleLink = $titleLink;
    }

    /**
     * Get titleLink.
     *
     * @return String
     */
    public function getTitleLink()
    {
        return $this->titleLink;
    }

    /**
     * Set author.
     *
     * @param string $author
     */
    public function setAuthor($author)
    {
        $this->author = $author;
    }

    /**
     * Get author.
     *
     * @return string
     */
    public function getAuthor()
    {
        return $this->author;
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
    public function isActive()
    {
        return $this->active;
    }

    /**
     * Set preview.
     *
     * @param String $preview
     */
    public function setPreview($preview)
    {
        $this->preview = $preview;
    }

    /**
     * Get preview.
     *
     * @return String
     */
    public function getPreview()
    {
        return $this->preview;
    }

    /**
     * Set text.
     *
     * @param String $text
     */
    public function setText($text)
    {
        $this->text = $text;
    }

    /**
     * Get text.
     *
     * @return String
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set order.
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
     * Set type.
     *
     * @param int $type
     */
    public function setType($type = 0)
    {
        $this->type = $type;
    }

    /**
     * Get type.
     *
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set date.
     *
     * @param String $date
     */
    public function setDate($date = "0000-00-00 00:00:00")
    {
        $this->date = $date;
    }

    /**
     * Get date.
     *
     * @return String
     */
    public function getDate()
    {
        return $this->date;
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
}
