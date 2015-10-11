<?php

/**
 * @copyright  2015 (c) Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 *
 * @version    0.0.17
 *
 * @link       TBA
 */

namespace Application\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Application\Exception\InvalidArgumentException;

final class SystemSettings extends AbstractPlugin
{
    /**
     * @var array $options
     */
    private $options = null;

    /**
     * @param array $options
     */
    public function __construct(array $options = null)
    {
        $this->options = $options;
    }

    /**
     * Shorthand method for requesting global system settings.
     *
     * @param string $option
     *
     * @return string
     */
    public function __invoke($option = 'general', $value = 'site_name')
    {
        switch ($option) {
            case 'general':
            case 'mail':
            case 'registration':
            case 'posts':
            case 'discussion':
                return $this->getOption($option, $value);
                break;

            default:
                throw new InvalidArgumentException("Option doesn't exists");

                break;
        }
    }

    /**
     * Get an individual option.
     *
     * Keys are normalized to lowercase.
     *
     * Returns null for unfound options.
     *
     * @param string $key
     * @param string $value
     *
     * @return mixed
     */
    private function getOption($key, $value)
    {
        $key = strtolower($key);
        $value = strtolower($value);
        if (array_key_exists($value, $this->options[$key])) {
            return $this->options[$key][$value];
        }

        return;
    }
}
