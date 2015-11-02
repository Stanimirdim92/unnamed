<?php

/**
 * @copyright  2015 (c) Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 *
 * @version    0.0.21
 *
 * @link       TBA
 */

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ResetPassword
 *
 * @ORM\Entity
 * @ORM\Table(name="resetpassword")
 */
final class ResetPassword
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
     * @ORM\Column(name="token", type="string", length=100, nullable=true)
     */
    private $token;

    /**
     * @var string
     *
     * @ORM\Column(name="ip", type="string", length=30, nullable=true)
     */
    private $ip;

    /**
     * @var string
     *
     * @ORM\Column(name="date", type="string", nullable=true)
     */
    private $date = "0000-00-00 00:00:00";

    /**
     * @var integer
     *
     * @ORM\Column(name="user", type="integer", nullable=false)
     */
    private $user = 0;

    /**
     * @param array $data
     *
     * @return mixed
     */
    public function exchangeArray(array $data = [])
    {
        $this->id = (isset($data['id'])) ? $data['id'] : $this->getId();
        $this->ip = (isset($data['ip'])) ? $data['ip'] :  $this->getIp();
        $this->date = (isset($data['date'])) ? $data['date'] :  $this->getDate();
        $this->token = (isset($data['token'])) ? $data['token'] :  $this->getToken();
        $this->user = (isset($data['user'])) ? $data['user'] :  $this->getUser();
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
     * @param  array $options
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
     * Set token.
     *
     * @param String $token
     */
    public function setToken($token)
    {
        $this->token = $token;
    }

    /**
     * Get token.
     *
     * @return String
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set ip.
     *
     * @param String $ip
     */
    public function setIp($ip)
    {
        $this->ip = $ip;
    }

    /**
     * Get ip.
     *
     * @return String
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * Set date.
     *
     * @param string $date
     */
    public function setDate($date = "0000-00-00 00:00:00")
    {
        $this->date = $date;
    }

    /**
     * Get date.
     *
     * @return string
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set user.
     *
     * @param Int $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * Get user.
     *
     * @return Int
     */
    public function getUser()
    {
        return $this->user;
    }
}
