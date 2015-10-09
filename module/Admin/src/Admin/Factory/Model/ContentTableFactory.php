<?php

/**
 * @copyright  2015 (c) Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 *
 * @version    0.0.16
 *
 * @link       TBA
 */

namespace Admin\Factory\Model;

use Zend\Db\ResultSet\ResultSet;
use Admin\Model\ContentTable;

final class ContentTableFactory
{
    /**
     * @{inheritDoc}
     */
    public function __invoke($serviceLocator)
    {
        $table = new ContentTable($serviceLocator->get('SD\Adapter'), new ResultSet());

        return $table;
    }
}
