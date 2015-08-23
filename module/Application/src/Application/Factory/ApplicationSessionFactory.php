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
 * @version    0.0.10
 * @link       TBA
 */

namespace Application\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Cache\StorageFactory;
use Zend\Session\SaveHandler\Cache;
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
            'cookie_lifetime'     => 7200, //2hrs
            'remember_me_seconds' => 7200, //2hrs This is also set in the login controller
            'use_cookies'         => true,
            'cache_expire'        => 180,  //3hrs
            'cookie_path'         => "/",
            'cookie_secure'       => $func::isSSL(),
            'cookie_httponly'     => true,
            'name'                => '__zpc',
        ]);
        $sessionManager = new SessionManager($sessionConfig);
        // $memCached = new StorageFactory::factory([
        //     'adapter' => [
        //        'name'     =>'memcached',
        //         'lifetime' => 7200,
        //         'options'  => [
        //             'servers'   => [
        //                 [
        //                     '127.0.0.1',11211
        //                 ],
        //             ],
        //             'namespace'  => 'MYMEMCACHEDNAMESPACE',
        //             'liboptions' => [
        //                 'COMPRESSION' => true,
        //                 'binary_protocol' => true,
        //                 'no_block' => true,
        //                 'connect_timeout' => 100
        //             ]
        //         ],
        //     ],
        // ]);

        // $saveHandler = new Cache($memCached);
        // $sessionManager->setSaveHandler($saveHandler);
        return $sessionManager;
    }
}
