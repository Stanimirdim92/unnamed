<?php
/**
 * MIT License.
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
 * @version    0.0.11
 * @link       TBA
 */

/**
 * All configurations options, used in two or more modules must go in here.
 */
return [
    'service_manager' => [
        'abstract_factories' => [
            'CacheAbstractFactory' => 'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
        ],
        'factories' => [
            'Zend\Db\Adapter\Adapter' => 'Application\Factory\AdapterServiceFactory',
            'AdminErrorHandling'      => 'Admin\Factory\AdminErrorHandlingFactory',
            'AdministratorTable'      => 'Admin\Factory\Model\AdministratorTableFactory',
            'ContentTable'            => 'Admin\Factory\Model\ContentTableFactory',
            'LanguageTable'           => 'Admin\Factory\Model\LanguageTableFactory',
            'MenuTable'               => 'Admin\Factory\Model\MenuTableFactory',
            'UserTable'               => 'Admin\Factory\Model\UserTableFactory',
            'AdminMenuTable'          => 'Admin\Factory\Model\AdminMenuTableFactory',
            'translator'              => 'Zend\Mvc\Service\TranslatorServiceFactory',
        ],
    ],
    'controller_plugins' => [
        'factories' => [
            'translate'         => 'Application\Controller\Plugin\Factory\TranslateFactory',
            'Mailing'           => 'Application\Controller\Plugin\Factory\MailingFactory',
            'UserData'          => 'Application\Controller\Plugin\Factory\UserDataFactory',
            'setLayoutMessages' => 'Application\Controller\Plugin\Factory\LayoutMessagesFactory',
            'InitMetaTags'      => 'Application\Controller\Plugin\Factory\InitMetaTagsFactory',
            'getParam'          => 'Application\Controller\Plugin\Factory\GetUrlParamsFactory',
            'getTable'          => 'Application\Controller\Plugin\Factory\GetTableModelFactory',
            'getFunctions'      => 'Application\Controller\Plugin\Factory\FunctionsFactory',
            'setErrorCode'      => 'Application\Controller\Plugin\Factory\ErrorCodesFactory',
        ],
    ],
    'translator' => [
        'locale' => 'en',
        'translation_file_patterns' => [
            [
                'base_dir' => __DIR__.'/../../module/Application/languages/phpArray',
                'type'     => 'phpArray',
                'pattern'  => '%s.php',
            ],
        ],
        'cache' => [
            'adapter' => [
                'name'    => 'Filesystem',
                'options' => [
                    'cache_dir' => __DIR__ . '/../../data/cache/frontend',
                    'ttl'       => '3600',
                ],
            ],
            'plugins' => [
                [
                    'name'    => 'serializer',
                    'options' => [],
                ],
                'exception_handler' => [
                    'throw_exceptions' => (APP_ENV === "development"),
                ],
            ],
        ],
    ],
    'view_manager' => [
        'display_not_found_reason' => (APP_ENV === "development"),
        'display_exceptions'       => (APP_ENV === "development"),
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/index',
        'exception_template'       => 'error/index',
        'default_template_suffix'  => 'phtml',
        'strategies' => [
            'ViewJsonStrategy',
        ],
    ],
];
