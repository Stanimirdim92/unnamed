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
 * User
 *
 * @ORM\Entity
 * @ORM\Table(name="user")
 */
final class User
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
     * @var string
     *
     * @ORM\Column(name="surname", type="string", length=100, nullable=true)
     */
    private $surname;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=72, nullable=true)
     */
    private $password;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=100, nullable=true)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="birthDate", type="string", nullable=true)
     */
    private $birthDate = "0000-00-00";

    /**
     * @var string
     *
     * @ORM\Column(name="lastLogin", type="string", nullable=true)
     */
    private $lastLogin = "0000-00-00 00:00:00";

    /**
     * @var boolean
     *
     * @ORM\Column(name="isDisabled", type="smallint", nullable=false)
     */
    private $isDisabled = false;

    /**
     * @var string
     *
     * @ORM\Column(name="image", type="string", length=100, nullable=true)
     */
    private $image;

    /**
     * @var string
     *
     * @ORM\Column(name="registered", type="string", nullable=true)
     */
    private $registered = "0000-00-00 00:00:00";

    /**
     * @var boolean
     *
     * @ORM\Column(name="hideEmail", type="smallint", nullable=false)
     */
    private $hideEmail = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="ip", type="string", length=30, nullable=true)
     */
    private $ip;

    /**
     * @var integer
     *
     * @ORM\Column(name="admin", type="integer", nullable=false)
     */
    private $admin = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="language", type="integer", nullable=false)
     */
    private $language = 1;

    /**
     * @param array $data
     */
    public function exchangeArray(array $data = [])
    {
        $this->id = (isset($data['id'])) ? $data['id'] : $this->getId();
        $this->name = (isset($data['name'])) ? $data['name'] : $this->getName();
        $this->surname = (isset($data['surname'])) ? $data['surname'] : $this->getSurname();
        $this->password = (isset($data['password'])) ? $data['password'] : $this->getPassword();
        $this->email = (isset($data['email'])) ? $data['email'] : $this->getEmail();
        $this->birthDate = (isset($data['birthDate'])) ? $data['birthDate'] : $this->getBirthDate();
        $this->lastLogin = (isset($data['lastLogin'])) ? $data['lastLogin'] : $this->getLastLogin();
        $this->isDisabled = (isset($data['isDisabled'])) ? $data['isDisabled'] : $this->isDisabled();
        $this->image = (isset($data['image'])) ? $data['image'] : $this->getImage();
        $this->registered = (isset($data['registered'])) ? $data['registered'] : $this->getRegistered();
        $this->hideEmail = (isset($data['hideEmail'])) ? $data['hideEmail'] : $this->getHideEmail();
        $this->ip = (isset($data['ip'])) ? $data['ip'] : $this->getIp();
        $this->admin = (isset($data['admin'])) ? $data['admin'] : $this->getAdmin();
        $this->language = (isset($data['language'])) ? $data['language'] : $this->getLanguage();
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
     * @param String $name
     */
    public function setName($name)
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
     * Set surname.
     *
     * @param String $surname
     */
    public function setSurname($surname)
    {
        $this->surname = $surname;
    }

    /**
     * Get surname.
     *
     * @return String
     */
    public function getSurname()
    {
        return $this->surname;
    }

    /**
     * Set password.
     *
     * @param String $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * Get password.
     *
     * @return String
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set email.
     *
     * @param String $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * Get email.
     *
     * @return String
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set BirthDate.
     *
     * @param String $birthDate
     */
    public function setBirthDate($birthDate = "0000-00-00")
    {
        $this->birthDate = $birthDate;
    }

    /**
     * Get birthDate.
     *
     * @return String
     */
    public function getBirthDate()
    {
        return $this->birthDate;
    }

    /**
     * Set lastLogin.
     *
     * @param String $lastLogin
     */
    public function setLastLogin($lastLogin = "0000-00-00 00:00:00")
    {
        $this->lastLogin = $lastLogin;
    }

    /**
     * Get lastLogin.
     *
     * @return String
     */
    public function getLastLogin()
    {
        return $this->lastLogin;
    }

    /**
     * Set isDisabled.
     *
     * @param bool $isDisabled
     */
    public function setDisabled($isDisabled = false)
    {
        $this->isDisabled = $isDisabled;
    }

    /**
     * Get isDisabled.
     *
     * @return int
     */
    public function isDisabled()
    {
        return $this->isDisabled;
    }

    /**
     * Set image.
     *
     * @param String $image
     */
    public function setImage($image)
    {
        $this->image = $image;
    }

    /**
     * Get image.
     *
     * @return String
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set registered.
     *
     * @param String $registered
     */
    public function setRegistered($registered = "0000-00-00 00:00:00")
    {
        $this->registered = $registered;
    }

    /**
     * Get registered.
     *
     * @return String
     */
    public function getRegistered()
    {
        return $this->registered;
    }

    /**
     * Set hideEmail.
     *
     * @param Boolean $hideEmail
     */
    public function setHideEmail($hideEmail = false)
    {
        $this->hideEmail = $hideEmail;
    }

    /**
     * Get hideEmail.
     *
     * @return Boolean
     */
    public function getHideEmail()
    {
        return $this->hideEmail;
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
     * Set language.
     *
     * @param Int $language
     */
    public function setLanguage($language = 1)
    {
        $this->language = $language;
    }

    /**
     * Get language.
     *
     * @return Int
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * Set admin.
     *
     * @param int $admin
     */
    public function setAdmin($admin = 0)
    {
        $this->admin = $admin;
    }

    /**
     * Get admin.
     *
     * @return Boolean
     */
    public function getAdmin()
    {
        return $this->admin;
    }

    /**
     * @return string
     */
    public function getFullName()
    {
        return $this->getName()." ".$this->getSurname();
    }
}
