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
use Zend\I18n\Translator\Translator;

final class Translate extends AbstractPlugin
{
    /**
     * @var Translator $translator
     */
    private $translator = null;

    /**
     * @param Translator $translator
     */
    public function __construct(Translator $translator = null)
    {
        $this->translator = $translator;
    }

    /**
     * @param string $message
     *
     * @return string
     */
    public function __invoke($message = '')
    {
        return $this->translator->translate($message);
    }
}
