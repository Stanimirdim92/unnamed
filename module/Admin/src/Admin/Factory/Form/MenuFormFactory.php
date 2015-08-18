<?php
/**
 * MIT License
 * ===========
 *
 * Copyright (c) 2015 Stanimir Dimitrov <stanimirdim92@gmail.com>
 *
 * Permission is hereby granted, free of charge, to any person obtaining
 * a copy of this software and associated documentation files (the
 * "Software"), to deal in the Software without restriction, including
 * without limitation the rights to use, copy, modify, merge, publish,
 * distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to
 * the following conditions:
 *
 * The above copyright notice and this permission notice shall be included
 * in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS
 * OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
 * IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY
 * CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT,
 * TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE
 * SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 *
 * @author     Stanimir Dimitrov <stanimirdim92@gmail.com>
 * @copyright  2015 (c) Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 * @version    0.0.7
 * @link       TBA
 */

namespace Admin\Factory\Form;

use Admin\Form\MenuForm;
use Zend\Session\Container;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class MenuFormFactory implements FactoryInterface
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

        $languages = $this->services->get("LanguageTable")->fetchList(false, [], ["active" => 1], "AND");

        $form = new MenuForm(
            $languages,
            $this->prepareMenusData()
        );

        return $form;
    }

    private function prepareMenusData()
    {
        $lang = new Container("translations");
        $menu = $this->services->get("MenuTable")->fetchList(false, ['id', 'menulink', 'caption', 'language', 'parent'], ["language" => $lang->language], "AND", null, "id, menuOrder");
        if (count($menu) > 0) {
            $menus = ["menus" => null, "submenus" => null];
            foreach ($menu as $submenu) {
                if ($submenu->getParent() > 0) {
                    /**
                     * This needs to have a second empty array in order to work
                     */
                    $menus["submenus"][$submenu->getParent()][] = $submenu;
                } else {
                    $menus["menus"][$submenu->getId()] = $submenu;
                }
            }
            return $menus;
        }
        return [];
    }
}
