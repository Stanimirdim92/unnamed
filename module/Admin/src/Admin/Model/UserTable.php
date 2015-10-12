<?php

/**
 * @copyright  2015 (c) Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 *
 * @version    0.0.17
 *
 * @link       TBA
 */

namespace Admin\Model;

use Admin\Model\AbstractModelTable;
use Admin\Exception\RuntimeException;

final class UserTable extends AbstractModelTable
{
    /**
     * @method __construct
     *
     * @param \Zend\Db\Adapter\Adapter $adapter
     */
    public function __construct($adapter)
    {
        parent::__construct('user', 'User', $adapter);
    }
    /**
     * @param int $id user id
     *
     * @return User|null
     */
    public function getUser($id = 0)
    {
        $rowset = $this->select(['id' => (int) $id]);
        $rowset->buffer();
        if (!$rowset->current()) {
            return;
        }
        return $rowset->current();
    }

    /**
     * This method can disable or enable user accounts.
     *
     * @param int $id user id
     * @param int $state 0 - enabled, 1 - disabled
     */
    public function toggleUserState($id = 0, $state = 0)
    {
        if ($this->getUser($id)) {
            $this->update(["isDisabled" => (int) $state, 'admin' => 0], ['id' => (int) $id]);
        }
    }

    /**
     * Update user based on the provided id.
     *
     * @param  User $use
     *
     * @throws RuntimeException
     *
     * @return User
     */
    public function saveUser(User $user)
    {
        $data = [
            'name'       => (string) $user->getName(),
            'surname'    => (string) $user->getSurname(),
            'password'   => (string) $user->getPassword(),
            'email'      => (string) $user->getemail(),
            'birthDate'  => (string) $user->getBirthDate(),
            'lastLogin'  => (string) $user->getLastLogin(),
            'isDisabled'    => (int) $user->isDisabled(),
            'image'      => (string) $user->getImage(),
            'registered' => (string) $user->getRegistered(),
            'hideEmail'  => (int) $user->getHideEmail(),
            'ip'         => (string) $user->getIp(),
            'admin'      => (int) $user->getAdmin(),
            'language'   => (int) $user->getLanguage(),
        ];

        $id = (int) $user->getId();
        if (!$id) {
            $this->insert($data);
        } else {
            if (!$this->getUser($id)) {
                throw new RuntimeException("User not saved");
            }
            $this->update($data, ['id' =>(int)  $id]);
        }
        unset($id, $data);
        return $user;
    }
}
