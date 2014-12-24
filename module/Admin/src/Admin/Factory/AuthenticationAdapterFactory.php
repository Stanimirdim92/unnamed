<?php 

namespace Admin\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Authentication\Adapter\Http as HttpAdapter;
use Zend\Authentication\Adapter\Http\FileResolver;

class AuthenticationAdapterFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config         = $serviceLocator->get('Config');
        $authConfig     = $config['admin']['auth_adapter'];
        $authAdapter    = new HttpAdapter($authConfig['config']);
        $basicResolver = new FileResolver();

        $basicResolver->setFile($authConfig['basic_passwd_file']);
        $authAdapter->setBasicResolver($basicResolver);

        return $authAdapter;
    }
}

?>