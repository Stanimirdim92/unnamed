<?php

/**
 * @copyright  2015 (c) Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 *
 * @version    0.0.21
 *
 * @link       TBA
 */

namespace Application\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\Mvc\Controller\Plugin\FlashMessenger;

final class GetLayoutMessages extends AbstractPlugin
{
    /**
     * @var FlashMessenger $flashMessenger
     */
    private $flashMessenger = null;

    public function __construct(FlashMessenger $flashMessenger = null)
    {
        $this->flashMessenger = $flashMessenger;
    }

    /**
     * @return array
     */
    public function __invoke()
    {
        return $this->flashMessenger;
    }
}
