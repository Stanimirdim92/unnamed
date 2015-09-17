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
        $this->layout->setVariables([
            'message' => '404 Not found',
            'reason' => 'The link you have requested doesn\'t exists',
            'exception' => "",
        ]);
        $this->layout->setTemplate('error/index');
    }
}
