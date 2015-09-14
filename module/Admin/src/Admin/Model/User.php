<?php
/**
 * MIT License
 * ===========
 *
 * Copyright (c) 2015 Stanimir Dimitrov <stanimirdim92@gmail.com>
 *
 * Permission is hereby granted, free of charge, to any person obtaining
 * a copy of this software and associated documentation files (the
 * "Software"), to deal in the Software without restriction, including
 * without limitation the rights to use, copy, modify, merge, publish,
 * distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to
 * the following conditions:
 *
 * The above copyright notice and this permission notice shall be included
 * in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS
 * OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
 * IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY
 * CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT,
 * TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE
 * SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 *
 * @author     Stanimir Dimitrov <stanimirdim92@gmail.com>
 * @copyright  2015 (c) Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 * @version    0.0.12
 * @link       TBA
 */

namespace Admin\Model;

class User
{
    /**
     * @var Int $id
     */
    private $id = 0;

    /**
     * @var String $name
     */
    private $name = null;

    /**
     * @var String $surname
     */
    private $surname = null;

    /**
     * @var string $password
     */
    private $password = null;

    /**
     * @var String $email
     */
    private $email = null;

    /**
     * @var string $birthDate
     */
    private $birthDate = "0000-00-00";

    /**
     * @var string $lastLogin
     */
    private $lastLogin = "0000-00-00 00:00:00";

    /**
     * @var Int $deleted
     */
    private $deleted = 0;

    /**
     * @var String $image
     */
    private $image = null;

    /**
     * @var string $registered
     */
    private $registered = "0000-00-00 00:00:00";

    /**
     * @var Int $hideEmail
     */
    private $hideEmail = 0;

    /**
     * @var String $ip
     */
    private $ip = null;

    /**
     * @var Int $admin
     */
    private $admin = 0;

    /**
     * @var Int $language
     */
    private $language = 1;

    /**
     * @param array $data
     * @return void
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
        $this->deleted = (isset($data['deleted'])) ? $data['deleted'] : $this->getDeleted();
        $this->image = (isset($data['image'])) ? $data['image'] : $this->getImage();
        $this->registered = (isset($data['registered'])) ? $data['registered'] : $this->getRegistered();
        $this->hideEmail = (isset($data['hideEmail'])) ? $data['hideEmail'] : $this->getHideEmail();
        $this->ip = (isset($data['ip'])) ? $data['ip'] : $this->getIp();
        $this->admin = (isset($data['admin'])) ? $data['admin'] : $this->getAdmin();
        $this->language = (isset($data['language'])) ? $data['language'] : $this->getLanguage();
    }

    /**
     * Used into form binding
     */
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

    /**
     * constructor
     *
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $this->exchangeArray($options);
    }

    /**
     * Get id
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set id
     * @param int
     */
    public function setId($id = 0)
    {
        $this->id = $id;
    }


    /**
     * Set name
     * @param String $name
     */
    public function setName($name = null)
    {
        $this->name = $name;
    }

    /**
     * Get name
     * @return String
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set surname
     * @param String $surname
     */
    public function setSurname($surname = null)
    {
        $this->surname = $surname;
    }

    /**
     * Get surname
     * @return String
     */
    public function getSurname()
    {
        return $this->surname;
    }

    /**
     * Set password
     * @param String $password
     */
    public function setPassword($password = null)
    {
        $this->password = $password;
    }

    /**
     * Get password
     * @return String
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set email
     * @param String $email
     */
    public function setEmail($email = null)
    {
        $this->email = $email;
    }

    /**
     * Get email
     * @return String
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set BirthDate
     * @param String $birthDate
     */
    public function setBirthDate($birthDate = "0000-00-00")
    {
        $this->birthDate = $birthDate;
    }

    /**
     * Get birthDate
     * @return String
     */
    public function getBirthDate()
    {
        return $this->birthDate;
    }

    /**
     * Set lastLogin
     * @param String $lastLogin
     */
    public function setLastLogin($lastLogin = "0000-00-00 00:00:00")
    {
        $this->lastLogin = $lastLogin;
    }

    /**
     * Get lastLogin
     * @return String
     */
    public function getLastLogin()
    {
        return $this->lastLogin;
    }

    /**
     * Set deleted
     * @param int $deleted
     */
    public function setDeleted($deleted = 0)
    {
        $this->deleted = $deleted;
    }

    /**
     * Get deleted
     * @return int
     */
    public function getDeleted()
    {
        return $this->deleted;
    }

    /**
     * Set image
     * @param String $image
     */
    public function setImage($image = null)
    {
        $this->image = $image;
    }

    /**
     * Get image
     * @return String
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set registered
     * @param String $registered
     */
    public function setRegistered($registered = "0000-00-00 00:00:00")
    {
        $this->registered = $registered;
    }

    /**
     * Get registered
     * @return String
     */
    public function getRegistered()
    {
        return $this->registered;
    }

    /**
     * Set hideEmail
     * @param Boolean $hideEmail
     */
    public function setHideEmail($hideEmail = 0)
    {
        $this->hideEmail = $hideEmail;
    }

    /**
     * Get hideEmail
     * @return Boolean
     */
    public function getHideEmail()
    {
        return $this->hideEmail;
    }

    /**
     * Set ip
     * @param String $ip
     */
    public function setIp($ip)
    {
        $this->ip = $ip;
    }

    /**
     * Get ip
     * @return String
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * Set language
     * @param Int $language
     */
    public function setLanguage($language = 1)
    {
        $this->language = $language;
    }

    /**
     * Get language
     * @return Int
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * Set admin
     * @param int $admin
     */
    public function setAdmin($admin = 0)
    {
        $this->admin = $admin;
    }

    /**
     * Get admin
     * @return Boolean
     */
    public function getAdmin()
    {
        return $this->admin;
    }

    /**
     * magic property exists (isset) checker
     */
    public function __isset($property)
    {
        return (property_exists($this, $property) ? isset($this->{$property}) : null);
    }

    /**
     * Serialize object or return it as an array
     *
     * @param  array $skip Remove the unnecessary objects from the array
     * @param  bool $serializable Should the function return a serialized object
     * @return array|string
     */
    public function getProperties(array $skip = [], $serializable = false)
    {
        $returnValue = [];
        $data = get_class_vars(get_class($this));
        foreach ($data as $key => $value) {
            if (!in_array($key, $skip)) {
                $returnValue[$key] = $this->{$key};
            }
        }
        if ((bool) $serializable === true) {
            return serialize($returnValue);
        }
        return $returnValue;
    }

    public function getFullName()
    {
        return $this->getName()." ".$this->getSurname();
    }
}
