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

use Admin\Form\ContentForm;
use Zend\Session\Container;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

final class ContentFormFactory implements FactoryInterface
{
    /**
     * @{inheritDoc}
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $lang = new Container("translations");

        $languages = $serviceLocator->getServiceLocator()->get("LanguageTable");
        $languages->where(["active" => 1]);
        $languages = $languages->fetch();

        $menu = $serviceLocator->getServiceLocator()->get("MenuTable");
        $menu->columns(['id', 'language', 'caption']);
        $menu->where(["active" => 1, "language" => $lang->language]);
        $menu->order("id, menuOrder");
        $menu = $menu->fetch();

        $form = new ContentForm($languages, $menu);

        return $form;
    }
}
