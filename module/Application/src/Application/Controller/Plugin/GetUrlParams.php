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
 * @copyright  2015 Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 * @version    0.0.10
 * @link       TBA
 */

namespace Application\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\Escaper\Escaper;
use Zend\Mvc\Controller\Plugin\Params;

final class GetUrlParams extends AbstractPlugin
{
    /**
     * @var Params $param
     */
    private $params = null;

    /**
     * @param Params $param
     */
    public function __construct(Params $params = null)
    {
        $this->params = $params;
    }

    /**
     * Shorthand method for getting params from URLs. Makes code easier to modify and avoids DRY code
     *
     * @param String $paramName
     * @param mixed $default
     * @return mixed
     */
    public function __invoke($paramName = null, $default = null)
    {
        $escaper = new Escaper('utf-8');

        $param = $this->params->fromPost($paramName, 0);
        if (!$param) {
            $param = $this->params->fromRoute($paramName, null);
        }
        if (!$param) {
            $param = $this->params->fromQuery($paramName, null);
        }
        if (!$param) {
            $param = $this->params->fromHeader($paramName, null);
        }
        if (!$param) {
            $param = $this->params->fromFiles($paramName, null);
        }

        /**
         * If this is array it MUST comes from fromFiles()
         */
        if (is_array($param) && !empty($param)) {
            return $param;
        }

        /**
         * It could be an empty array or any negative value. In this case return the default value.
         */
        if ((is_array($param) && empty($param)) || !$param) {
            return $default;
        }

        return $escaper->escapeHtml($param);
    }
}
