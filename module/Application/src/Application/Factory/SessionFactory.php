<?php

/**
 * @copyright  2015 (c) Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 *
 * @version    0.0.21
 *
 * @link       TBA
 */

namespace Application\Factory;

use Zend\Session\SessionManager;
use Zend\Session\Config\SessionConfig;

final class SessionFactory
{
    /**
     * {@inheritDoc}
     */
    public function __invoke()
    {
        $sessionConfig = new SessionConfig();
        $sessionConfig->setOptions(
            [
            'cookie_lifetime'         => 7200, //2hrs
            'remember_me_seconds'     => 7200, //2hrs This is also set in the login controller
            'use_cookies'             => true,
            'cache_expire'            => 180,  //3hrs
            'cookie_path'             => "/",
            'cookie_httponly'         => true,
            'name'                    => '__zpc',
            'cookie_secure'           => static::isSSL(),
            'hash_bits_per_character' => 6,
            'hash_function'           => 1,
            ]
        );
        $sessionManager = new SessionManager($sessionConfig);

        return $sessionManager;
    }

    /**
     * Detect SSL/TLS protocol. If true activate cookie_secure key.
     * Same code as the one from Functions.php, but this way it's skips the call to SM. It saves 3 function calls.
     *
     * @return bool
     */
    private static function isSSL()
    {
        $stats = false;
        if (isset($_SERVER['HTTPS'])) {
            if ('on' == strtolower($_SERVER['HTTPS']) || '1' == $_SERVER['HTTPS']) {
                $stats = true;
            }
        } elseif (isset($_SERVER['SERVER_PORT']) && ('443' == $_SERVER['SERVER_PORT'])) {
            $stats = true;
        }
        return $stats;
    }
}
