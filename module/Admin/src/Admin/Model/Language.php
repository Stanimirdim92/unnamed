<?php

/**
 * @copyright  2015 (c) Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 *
 * @version    0.0.16
 *
 * @link       TBA
 */

namespace Admin\Model;

final class Language
{
    /**
     * @var Int $id
     */
    private $id = 0;

    /**
     * @var null $name
     */
    private $name = null;

    /**
     * @var bool $active
     */
    private $active = 1;

    /**
     * @var array $data
     */
    public function exchangeArray(array $data = [])
    {
        $this->id = (isset($data['id'])) ? $data['id'] : $this->getId();
        $this->name = (isset($data['name'])) ? $data['name'] : $this->getName();
        $this->active = (isset($data['active'])) ? $data['active'] : $this->getActive();
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
     * Set name.
     *
     * @param null $name
     */
    public function setName($name = null)
    {
        $this->name = $name;
    }

    /**
     * Get name.
     *
     * @return String
     */
    public function getName()
    {
        return $this->name;
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
     * This method is a copy constructor that will return a copy object (except for the id field).
     * Note that this method will not save the object.
     */
    public function getCopy()
    {
        $copy = new self();
        $copy->setName($this->getName());
        $copy->setActive($this->getActive());
        return $copy;
    }
}
