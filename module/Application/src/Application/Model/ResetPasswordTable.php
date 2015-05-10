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
 * @category   Application\ResetPassword
 * @package    ZendPress
 * @author     Stanimir Dimitrov <stanimirdim92@gmail.com>
 * @copyright  2015 Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 * @version    0.03
 * @link       TBA
 */
namespace Application\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\ServiceManager\ServiceManager;

class ResetPasswordTable
{
    /**
     * @var TableGateway
     */
    private $tableGateway = null;

    /**
     * @var ServiceManager
     */
    private $serviceManager = null;

    public function __construct(ServiceManager $sm = null, TableGateway $tg = null)
    {
        $this->serviceManager = $sm;
        $this->tableGateway = $tg;
    }

    /**
     * @param int $id password id
     * @param int $id user id
     * @throws Exception If content is not found
     * @return ResetPassword
     */
    public function getResetPassword($id = 0, $user = 0)
    {
        $rowset = $this->tableGateway->select(['id' => (int) $id, 'user' => (int) $user]);
        if (!$rowset->current()) {
            throw new \RuntimeException("Couldn't find password");
        }
        return $rowset->current();
    }

    /**
     * Save or update password based on the provided id
     *
     * @param  ResetPassword|null $resetpassword
     * @throws Exception If resetpassword is not found
     * @return ResetPassword
     */
    public function saveResetPassword(ResetPassword $resetpw = null)
    {
        $data = [
            'ip'    => (string) $resetpw->ip,
            'user'  => (int) $resetpw->user,
            'date'  => (string) $resetpw->date,
            'token' => (string) $resetpw->token,
        ];
        $id = (int) $resetpw->id;
        $user = (int) $resetpw->user;
        if (!$id) {
            $this->tableGateway->insert($data);
            $resetpw->id = $this->tableGateway->lastInsertValue;
        } else {
            if (!$this->getResetPassword($id, $user)) {
                throw new \RuntimeException("Oops error.");
            }
            $this->tableGateway->update($data, ['id' => (int) $id, 'user' => (int) $user]);
        }
        unset($id, $user, $data);
        return $resetpw;
    }
}
