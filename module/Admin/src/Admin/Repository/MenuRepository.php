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
use Zend\Session\Container;

class MenuRepository extends EntityRepository
{
    /**
     * @return arrayobject
     */
    public function getMenus()
    {
        $lang = new Container("translations");
        return $this->createQueryBuilder('m')->select('m')
                    ->where("m.active = 1 AND m.language = '{$lang->language}'")
                    ->orderBy('m.parent', 'ASC')
                    ->getQuery()->getResult();
    }
}
