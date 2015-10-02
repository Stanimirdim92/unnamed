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

use Admin\Form\ContentForm;
use Zend\Session\Container;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ContentFormFactory implements FactoryInterface
{
    /**
     * @var ServiceManager
     */
    private $services = null;

    /**
     * @{inheritDoc}
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $this->services = $serviceLocator->getServiceLocator();
        $lang = new Container("translations");

        $languages = $this->services->get("LanguageTable")->fetchList(false, [], ["active" => 1], "AND");
        $menu = $this->services->get("MenuTable")->fetchList(false, ['id', 'language', 'caption'], ["active" => 1, "language" => $lang->language], "AND", null, "id, menuOrder");

        $form = new ContentForm($languages, $menu);

        return $form;
    }
}
