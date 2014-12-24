<?php 

namespace Admin\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Db\Adapter\Adapter;

class AdapterServiceFactory implements FactoryInterface
{

  protected $configKey;

  public function __construct($key)
  {
      $this->configKey = $key;
  }

  public function createService(ServiceLocatorInterface $serviceLocator)
  {
      $config = $serviceLocator->get('Config');
      return new Adapter($config[$this->configKey]);
  }
}

?>