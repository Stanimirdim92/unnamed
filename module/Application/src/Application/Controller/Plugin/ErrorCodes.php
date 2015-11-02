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
use Zend\Http\PhpEnvironment\Response;
use Zend\View\Model\ViewModel;

final class ErrorCodes extends AbstractPlugin
{
    /**
     * @var ViewModel $layout
     */
    private $layout = null;

    /**
     * @var Response $response
     */
    private $response = null;

    /**
     * @param ViewModel $layout
     * @param Response $response
     */
    public function __construct(ViewModel $layout = null, Response $response = null)
    {
        $this->layout = $layout;
        $this->response = $response;
    }

    /**
     * @param int $code error code
     */
    public function __invoke($code = 404)
    {
        $this->response->setStatusCode((int) $code);
        $this->layout->setVariables(
            [
            'message' => '404 Not found',
            'reason' => 'The link you have requested doesn\'t exists',
            'exception' => "",
            ]
        );
        $this->layout->setTemplate('error/index');
    }
}
