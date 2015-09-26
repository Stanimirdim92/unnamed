<?php

/**
 * @copyright  2015 (c) Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 *
 * @version    0.0.14
 *
 * @link       TBA
 */

namespace Application\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Session\SessionManager;
use Zend\Session\Config\SessionConfig;

class ApplicationSessionFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function createService(ServiceLocatorInterface $serviceLocator = null)
    {
        $sessionConfig = new SessionConfig();
        $func = $serviceLocator->get("ControllerPluginManager")->get("getfunctions");
        $sessionConfig->setOptions([
            'cookie_lifetime'         => 7200, //2hrs
            'remember_me_seconds'     => 7200, //2hrs This is also set in the login controller
            'use_cookies'             => true,
            'cache_expire'            => 180,  //3hrs
            'cookie_path'             => "/",
            'cookie_httponly'         => true,
            'name'                    => '__zpc',
            'cookie_secure'           => $func::isSSL(),
            'hash_bits_per_character' => 6,
        ]);
        $sessionManager = new SessionManager($sessionConfig);

        return $sessionManager;
    }
}
