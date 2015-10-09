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

class Administrator
{
    /**
     * @var Int $id
     */
    private $id = 0;

    /**
     * @param Int $user
     */
    private $user = null;

    /**
     * @var array $data
     */
    public function exchangeArray(array $data = [])
    {
        $this->id = (isset($data['id'])) ? $data['id'] : $this->getId();
        $this->user = (isset($data['user'])) ? $data['user'] : $this->getUser();
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
     * Set User.
     *
     * @param String $user
     */
    public function setUser($user = 0)
    {
        $this->user = $user;
    }

    /**
     * Get user.
     *
     * @return String
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * This method is a copy constructor that will return a copy object (except for the id field).
     * Note that this method will not save the object.
     */
    public function getCopy()
    {
        $clone = new self();
        $clone->setUser($this->getUser());
        return $clone;
    }
}
