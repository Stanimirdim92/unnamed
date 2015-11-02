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

class LanguageRepository extends EntityRepository
{
    /**
     * @return arrayobject
     */
    public function getLanguages()
    {
        return $this->createQueryBuilder('l')->select('l')
                    ->where('l.active = 1')
                    ->orderBy('l.id', 'ASC')
                    ->getQuery()->getResult();
    }
}
