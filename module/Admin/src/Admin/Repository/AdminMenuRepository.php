<?php

/**
 * @copyright  2015 (c) Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 *
 * @version    0.0.21
 *
 * @link       TBA
 */

namespace Admin\Repository;

use Doctrine\ORM\EntityRepository;

class AdminMenuRepository extends EntityRepository
{
    /**
     * @return arrayobject
     */
    public function getParentMenus()
    {
        return $this->createQueryBuilder('m')->select('m')
                    ->where("m.parent = '0'")
                    ->getQuery()->getResult();
    }
}
