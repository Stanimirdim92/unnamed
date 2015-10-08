<?php

/**
 * @copyright  2015 (c) Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 *
 * @version    0.0.15
 *
 * @link       TBA
 */

namespace Admin\Factory\Form;

use Admin\Form\SettingsPostsForm;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class SettingsPostsFormFactory implements FactoryInterface
{
    /**
     * @{inheritDoc}
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->getServiceLocator()->get("Config");

        $form = new SettingsPostsForm($config['system_config']["posts"]);

        return $form;
    }
}