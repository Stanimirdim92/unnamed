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
 * @category   Admin\Content
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

class ContentForm extends Form
{
    /**
     * Create the content form
     *
     * @param \Admin\Model\Content|null $options   holds the Content object
     * @param array                     $menus     \Admin\Model\Menu arrayobject
     * @param array                     $submenus     \Admin\Model\Menu
     * @param array                     $languages ResultSet arrayobject
     */
    public function __construct(\Admin\Model\Content $options = null, array $menus = [], array $submenus = [], $languages = [])
    {
        parent::__construct("content");
        $elements = [];


        $elements[99] = new Element\File('preview');
        $elements[99]->setLabel('Image')
                      ->setAttribute('id', 'preview')
                      ->setAttribute('class', 'preview');

        $elements[100] = new Element\File('imageUpload');
        $elements[100]->setLabel('Image')
                      ->setAttribute('id', 'imgajax')
                      ->setAttribute('class', 'imgupload')
                      ->setAttribute('multiple', true);

        $elements[0] = new Element\Text('title');
        $elements[0]->setLabel('Title');
        $elements[0]->setAttributes([
            'required'   => true,
            'size'        => 40,
            'id'         => "seo-caption",
            'placeholder' => 'Title',
        ]);

        if ($options!=null and $options->title) {
            $elements[0]->setValue($options->title);
        }

        $elements[3] = new Element\Textarea('text');
        $elements[3]->setLabel('Text')
                          ->setAttribute('class', 'ckeditor')
                          ->setAttribute('rows', 5)
                          ->setAttribute('cols', 80);
        if ($options!=null and $options->text) {
            $elements[3]->setValue($options->text);
        }

        $elements[4] = new Element\Select('menuOrder');
        $elements[4]->setLabel('Menu order');
        $valueOptions = [];
        for ($i = 1; $i<100; $i++) {
            $valueOptions[$i] = $i;
        }
        $elements[4]->setValueOptions($valueOptions);
        if ($options!=null and $options->menuOrder) {
            $elements[4]->setValue($options->menuOrder);
        } else {
            $elements[4]->setValue(0);
        }

        $elements[5] = new Element\Select('type');
        $elements[5]->setLabel('type');
        $valueOptions = [];
        $valueOptions[0] = "menu";
        $valueOptions[1] = "news";
        $elements[5]->setValueOptions($valueOptions);
        if ($options!=null and $options->type) {
            $elements[5]->setValue($options->type);
        }

        $elements[6] = new Element\Text('date');
        $elements[6]->setLabel('Date')
                        ->setAttribute('size', 20);
        if ($options!=null and $options->date) {
            $elements[6]->setValue($options->date);
        } else {
            $elements[6]->setValue(date("Y-m-d H:i:s"));
        }

        $elements[7] = new Element\Select('menu');
        $elements[7]->setLabel('menu');
        $valueOptions = [];
        $valueOptions[0] = 'Select a menu';

        foreach ($menus as $key => $menu) {
            $menu->setServiceManager(null);
            $valueOptions[$menu->getId()] = $menu->getCaption();

            if(!empty($submenus[$key])) {
                foreach($submenus[$key] as $sub) {
                    $sub->setServiceManager(null);
                    $valueOptions[$sub->getId()] = "--".$sub->getCaption();
                }
            }
        }
        $elements[7]->setValueOptions($valueOptions);

        if ($options!=null and $options->menu) {
            $elements[7]->setValue($options->menu);
        }

        $elements[8] = new Element\Select('language');
        $elements[8]->setLabel('language');
        $valueOptions = [];

        foreach ($languages as $item) {
            $valueOptions[$item->id] = $item->toString();
        }
        $elements[8]->setValueOptions($valueOptions);
        if ($options!=null and $options->language) {
            $elements[8]->setValue($options->language);
        }

        $elements[9] = new Element\Submit('submit');
        $elements[9]->setAttribute('id', 'submitbutton');

        if ($options!=null) {
            $elements[10] = new Element\Hidden('id');
            $elements[10]->setValue($options->id);
        }
        $elements[78] = new Element\Hidden('titleLink');
        $elements[78]->setAttribute('id', 'titleLink');
        if ($options!=null) {
            $elements[78]->setValue($options->titleLink);
        }


$elements[79]  = new Element\Hidden('progress_key');
$elements[79]->setAttribute('id', 'progress_key');
$elements[79]->setValue(md5(uniqid(rand())));

        $elements[88] = new Element\Csrf('s');
        foreach ($elements as $e) {
            $this->add($e);
        }

        // if there are no menus for the current session language, simply remove the menu.
        if (count($menus) <= 0) {
            $this->remove("menu");
        }
    }
}
