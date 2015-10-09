<?php

/**
 * @copyright  2015 (c) Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 *
 * @version    0.0.16
 *
 * @link       TBA
 */

namespace Application\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Db\Adapter\Adapter;
use BjyProfiler\Db\Adapter\ProfilingAdapter;
use BjyProfiler\Db\Profiler\Profiler;

final class AdapterServiceFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function createService(ServiceLocatorInterface $serviceLocator = null)
    {
        $config = $serviceLocator->get('Config');

        if (APP_ENV === 'development') {
            $adapter = new ProfilingAdapter($config["db"]);
            $adapter->setProfiler(new Profiler());
            $adapter->injectProfilingStatementPrototype();
            return $adapter;
        }
        return new Adapter($config["db"]);
    }
}
