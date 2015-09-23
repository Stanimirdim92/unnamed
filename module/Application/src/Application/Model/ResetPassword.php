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
 * @version    0.0.13
 * @link       TBA
 */

namespace Application\Model;

class ResetPassword
{
    /**
     * @var Int $id
     */
    private $id = 0;

    /**
     * @var string $password
     */
    private $token = null;

    /**
     * @var string $ip
     */
    private $ip = null;

    /**
     * @var string $date
     */
    private $date = "0000-00-00 00:00:00";

    /**
     * @var int $user
     */
    private $user = 0;

    /**
     * @param array $data
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
     * Used into form binding
     */
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

    /**
     * constructor
     * @param  array $options
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
     * Set token
     * @param String $token
     */
    public function setToken($token = null)
    {
        $this->token = $token;
    }

    /**
     * Get token
     * @return String
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set ip
     * @param String $ip
     */
    public function setIp($ip = null)
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
     * Set date
     * @param Int $date
     */
    public function setDate($date = "0000-00-00 00:00:00")
    {
        $this->date = $date;
    }

    /**
     * Get date
     * @return Int
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set user
     * @param Int $user
     */
    public function setUser($user = null)
    {
        $this->user = $user;
    }

    /**
     * Get user
     * @return Int
     */
    public function getUser()
    {
        return $this->user;
    }
}
