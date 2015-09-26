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

class AdminMenu
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
     * @param Int $advanced
     */
    private $advanced = 0;

    /**
     * @param String $controller
     */
    private $controller = null;

    /**
     * @param String $action
     */
    private $action = null;

    /**
     * @param String $class
     */
    private $class = null;

    /**
     * @param String $description
     */
    private $description = null;

    /**
     * @param Int $parent
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
        $this->advanced = (isset($data['advanced'])) ? $data['advanced'] : $this->getAdvanced();
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
     * Set advanced.
     *
     * @param Boolean $advanced
     */
    public function setAdvanced($advanced = 0)
    {
        $this->advanced = $advanced;
    }

    /**
     * Get advanced.
     *
     * @return Boolean
     */
    public function getAdvanced()
    {
        return $this->advanced;
    }

    /**
     * Set controller.
     *
     * @param String $controller
     */
    public function setController($controller = null)
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
    public function setAction($action = null)
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
     * Set description.
     *
     * @param string $description
     */
    public function setDescription($description = null)
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

    /**
     * This method is a copy constructor that will return a copy object (except for the id field).
     * Note that this method will not save the object.
     */
    public function getCopy()
    {
        $clone = new self();
        $clone->setCaption($this->getCaption());
        $clone->setMenuOrder($this->getMenuOrder());
        $clone->setAdvanced($this->getAdvanced());
        $clone->setController($this->getController());
        $clone->setAction($this->getAction());
        $clone->setClass($this->getClass());
        $clone->setDescription($this->getDescription());
        $clone->setParent($this->getParent());
        return $clone;
    }
}
