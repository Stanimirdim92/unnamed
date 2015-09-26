<?php

/**
 * @copyright  2015 (c) Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 *
 * @version    0.0.14
 *
 * @link       TBA
 */

namespace Admin\Factory\Form;

use Admin\Form\AdminMenuForm;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AdminMenuFormFactory implements FactoryInterface
{
    /**
     * @{inheritDoc}
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $parents = $serviceLocator->getServiceLocator()->get("AdminMenuTable")->fetchList(false, ["caption", "id"], ["parent" => 0]);

        $valueOptions = [];
        if (count($parents) > 0) {
            foreach ($parents as $parent) {
                $valueOptions[$parent->getId()] = $parent->getCaption();
            }
        }

        $form = new AdminMenuForm($valueOptions);

        return $form;
    }
}
