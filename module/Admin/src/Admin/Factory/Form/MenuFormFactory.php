<?php

/**
 * @copyright  2015 (c) Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 *
 * @version    0.0.18
 *
 * @link       TBA
 */

namespace Admin\Factory\Form;

use Admin\Form\MenuForm;
use Zend\Session\Container;
use Zend\ServiceManager\ServiceLocatorInterface;

final class MenuFormFactory
{
    /**
     * @{inheritDoc}
     */
    public function __invoke(ServiceLocatorInterface $serviceLocator)
    {
        $services = $serviceLocator->getServiceLocator();
        $lang = new Container("translations");

        $languages = $services->get("Admin\Model\LanguageTable")
                              ->getEntityRepository()
                              ->findBy(["active" => 1]);

        $menu = $services->get("Admin\Model\MenuTable")
                              ->getEntityRepository()
                              ->findBy(["active" => 1, "language" => $lang->language], ["parent" => "DESC"]);

        $entityManager = $services->get('Doctrine\ORM\EntityManager');

        $form = new MenuForm($entityManager);

        return $form;
    }
}
