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
 * Language
 *
 * @ORM\Entity
 * @ORM\Table(name="language")
 * @ORM\Entity(repositoryClass="Admin\Repository\LanguageRepository")
 */
final class Language
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
     * @ORM\Column(name="name", type="string", length=100, nullable=true)
     */
    private $name;

    /**
     * @var boolean
     *
     * @ORM\Column(name="active", type="smallint", nullable=false)
     */
    private $active = 1;

    /**
     * @var array $data
     */
    public function exchangeArray(array $data = [])
    {
        $this->id = (isset($data['id'])) ? $data['id'] : $this->getId();
        $this->name = (isset($data['name'])) ? $data['name'] : $this->getName();
        $this->active = (isset($data['active'])) ? $data['active'] : $this->isActive();
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
     *
     * @return $this
     */
    public function setId($id = 0)
    {
        $this->id = $id;

        return $this;
    }


    /**
     * Set name.
     *
     * @param null $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
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
     * @param bool|int $active
     *
     * @return $this
     */
    public function setActive($active = 0)
    {
        $this->active = $active;

        return $this;
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
}
