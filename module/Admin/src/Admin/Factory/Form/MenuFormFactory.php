<?php

/**
 * @copyright  2015 (c) Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 *
 * @version    0.0.17
 *
 * @link       TBA
 */

namespace Admin\Factory\Form;

use Admin\Form\MenuForm;
use Zend\Session\Container;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

final class MenuFormFactory implements FactoryInterface
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

        $languages = $this->services->get("LanguageTable");
        $languages->where( ["active" => 1]);
        $languages = $languages->fetch();

        $menu = $this->services->get("MenuTable");
        $menu->columns(['id', 'language', 'caption']);
        $menu->where(["active" => 1, "language" => $lang->language]);
        $menu->order("id, menuOrder");
        $menu = $menu->fetch();

        $form = new MenuForm($languages, $menu);

        return $form;
    }
}
