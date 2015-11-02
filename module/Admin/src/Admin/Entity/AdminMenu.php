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
 * AdminMenu
 *
 * @ORM\Entity
 * @ORM\Table(name="adminmenu")
 * @ORM\Entity(repositoryClass="Admin\Repository\AdminMenuRepository")
 */
final class AdminMenu
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
     * @ORM\Column(name="caption", type="string", length=150, nullable=true)
     */
    private $caption;

    /**
     * @var integer
     *
     * @ORM\Column(name="menuOrder", type="integer", nullable=false)
     */
    private $menuOrder = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="controller", type="string", length=50, nullable=true)
     */
    private $controller;

    /**
     * @var string
     *
     * @ORM\Column(name="action", type="string", length=50, nullable=true)
     */
    private $action;

    /**
     * @var string
     *
     * @ORM\Column(name="class", type="string", length=50, nullable=true)
     */
    private $class;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=150, nullable=true)
     */
    private $description;

    /**
     * @var integer
     *
     * @ORM\Column(name="parent", type="integer", nullable=false)
     */
    private $parent = 0;

    /**
     * @var array $data
     */
    public function exchangeArray(array $data = [])
    {
        $this->id = (isset($data['id'])) ? $data['id'] : $this->getId();
        $this->caption = (isset($data['caption'])) ? $data['caption'] : $this->getCaption();
        $this->menuOrder = (isset($data['menuOrder'])) ? $data['menuOrder'] : $this->getMenuOrder();
        $this->controller = (isset($data['controller'])) ? $data['controller'] : $this->getController();
        $this->action = (isset($data['action'])) ? $data['action'] : $this->getAction();
        $this->class = (isset($data['class'])) ? $data['class'] : $this->getClass();
        $this->description = (isset($data['description'])) ? $data['description'] : $this->getDescription();
        $this->parent = (isset($data['parent'])) ? $data['parent'] : $this->getParent();
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
    public function setCaption($caption)
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
     * Set controller.
     *
     * @param String $controller
     */
    public function setController($controller)
    {
        $this->controller = $controller;
    }

    /**
     * Get controller.
     *
     * @return String
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * Set action.
     *
     * @param String $action
     */
    public function setAction($action)
    {
        $this->action = $action;
    }

    /**
     * Get action.
     *
     * @return String
     */
    public function getAction()
    {
        return $this->action;
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
     * Set description.
     *
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * Get description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
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
}
