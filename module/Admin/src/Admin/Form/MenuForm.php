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
 * @category   Admin\Menu
 * @package    ZendPress
 * @author     Stanimir Dimitrov <stanimirdim92@gmail.com>
 * @copyright  2015 Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 * @version    0.03
 * @link       TBA
 */

namespace Admin\Form;

use Zend\Form\Form;
use Zend\Form\Element;

class MenuForm extends Form
{
    /**
     * Create the menu form
     *
     * @param \Admin\Model\Menu|null $options   holds the Menu object
     * @param array              $languages     ResultSet arrayobject
     * @param array              $parents       ResultSet arrayobject
     */
    public function __construct(\Admin\Model\Menu $options = null,  $languages = [], $parents = [])
    {
        parent::__construct("menu");
        $elements = [];

        $elements[0] = new Element\Text('caption');
        $elements[0]->setLabel('Caption');
        $elements[0]->setAttributes([
            'required'   => true,
            'id'         => "seo-caption",
            'size'        => 40,
            'placeholder' => 'Caption',
        ]);

        if ($options!=null and $options->caption) {
            $elements[0]->setValue($options->caption);
        }

        $elements[1] = new Element\Select('menuOrder');
        $elements[1]->setLabel('Menu order');
        $valueOptions = [];
        for ($i = 1; $i<40; $i++) {
            $valueOptions[$i] = $i;
        }
        $elements[1]->setValueOptions($valueOptions);
        if ($options!=null and $options->menuOrder) {
            $elements[1]->setValue($options->menuOrder);
        } else {
            $elements[1]->setValue(0);
        }

        $elements[2] = new Element\Text('keywords');
        $elements[2]->setLabel('Keywords');
        $elements[2]->setAttributes([
            'required'   => false,
            'size'        => 40,
            'placeholder' => 'Keywords (max 15 words) seperate by commas',
        ]);
        if ($options!=null and $options->keywords) {
            $elements[2]->setValue($options->keywords);
        }

        $elements[3] = new Element\Text('description');
        $elements[3]->setLabel('Description');
        $elements[3]->setAttributes([
            'required'   => false,
            'size'        => 40,
            'placeholder' => 'Description (max 150 characters)',
        ]);
        if ($options!=null and $options->description) {
            $elements[3]->setValue($options->description);
        }

        $elements[4] = new Element\Select('language');
        $elements[4]->setLabel('language');
        $valueOptions = [];

        foreach ($languages as $item) {
            $valueOptions[$item->id] = $item->toString();
        }
        $elements[4]->setValueOptions($valueOptions);
        if ($options!=null and $options->language) {
            $elements[4]->setValue($options->language);
        }

        $elements[5] = new Element\Select('parent');
        $elements[5]->setLabel('parent');
        $valueOptions = [];

        $valueOptions[0] = 'Select parent menu';
        foreach ($parents as $item) {
            if ($item->parent != 0) {
                $valueOptions[$item->id] = "--".$item->toString();
            } else {
                $valueOptions[$item->id] = $item->toString();
            }
        }
        $elements[5]->setValueOptions($valueOptions);
        if ($options!=null and $options->parent) {
            $elements[5]->setValue($options->parent);
        }

        $elements[6] = new Element\Select('menutype');
        $elements[6]->setLabel('Choose menu type');
        $valueOptions = [];
        $valueOptions[0] = "Main menu";
        $valueOptions[1] = "Left menu";
        $valueOptions[2] = "Right menu";
        $valueOptions[3] = "Footer menu";
        $elements[6]->setValueOptions($valueOptions)->setAttribute("id", "menutype");
        if ($options!=null and $options->menutype) {
            $elements[6]->setValue($options->menutype);
        }

        $elements[7] = new Element\Select('footercolumn');
        $elements[7]->setLabel('Choose footer column');
        $valueOptions = [];
        $valueOptions[1] = "Column one";
        $valueOptions[2] = "Column two";
        $valueOptions[3] = "Column three";
        $valueOptions[4] = "Column four";
        $elements[7]->setValueOptions($valueOptions);
        if ($options!=null and $options->footercolumn) {
            $elements[7]->setValue($options->footercolumn);
        }

        $elements[66] = new Element\Submit('submit');
        $elements[66]->setAttribute('id', 'submitbutton');

        $elements[69] = new Element\Csrf('s');

        if ($options!=null) {
            $elements[77] = new Element\Hidden('id');
            $elements[77]->setValue($options->id);
        }
        $elements[78] = new Element\Hidden('menulink');
        $elements[78]->setAttribute('id', 'menulink');
        if ($options!=null) {
            $elements[78]->setValue($options->menulink);
        }

        foreach ($elements as $e) {
            $this->add($e);
        }

        // if there is only one main menu or no menus at all remove the parent input and set menu.parent=0.
        if (count($parents) <= 1) {
            $this->remove("parent");
        }
    }
}
