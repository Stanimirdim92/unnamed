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
use Zend\Escaper\Escaper;
use Zend\Mvc\Controller\Plugin\Params;

final class GetUrlParams extends AbstractPlugin
{
    /**
     * @var Params $params
     */
    private $params = null;

    /**
     * @param Params $params
     */
    public function __construct(Params $params = null)
    {
        $this->params = $params;
    }

    /**
     * Shorthand method for getting params from URLs. Makes code easier to edit and avoids DRY code.
     *
     * @param String $paramName
     * @param mixed $default
     *
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

        /*
         * If this is array it MUST comes from fromFiles()
         */
        if (is_array($param) && !empty($param)) {
            return $param;
        }

        /*
         * It could be an empty array or any negative value. In this case return the default value.
         */
        if ((is_array($param) && empty($param)) || !$param) {
            return $default;
        }

        return $escaper->escapeHtml($param);
    }
}
