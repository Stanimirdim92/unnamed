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

use Admin\Form\AdminMenuForm;
use Zend\ServiceManager\ServiceLocatorInterface;

final class AdminMenuFormFactory
{
    /**
     * @{inheritDoc}
     */
    public function __invoke(ServiceLocatorInterface $serviceLocator)
    {
        $parents = $serviceLocator->getServiceLocator()->get("AdminMenuTable");
        $parents->columns(["caption", "id"]);
        $parents->where(["parent" => 0]);
        $parents = $parents->fetch();

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
