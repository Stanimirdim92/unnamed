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
 * @version    0.0.6
 * @link       TBA
 */

namespace Admin\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;

class MenuForm extends Form implements InputFilterProviderInterface
{
    public function __construct()
    {
        parent::__construct("menu");
    }

    public function init()
    {
        $this->setAttribute('method', 'post');

        $this->add([
            'type' => 'Zend\Form\Element\Text',
            'name' => 'caption',
            'attributes' => [
                'required'   => true,
                'size'        => 40,
                'id'         => "seo-caption",
                'placeholder' => 'Caption',
            ],
            'options' => [
                'label' => 'Caption',
            ],
        ]);

        $valueOptions = [];
        for ($i = 1; $i < 150; $i++) {
            $valueOptions[$i] = $i;
        }
        $this->add([
            'type' => 'Zend\Form\Element\Select',
            'name' => 'menuOrder',
            'options' => [
                'empty_option' => 'Please choose menu order (optional)',
                'value_options' => $valueOptions,
                'label' => 'Menu order',
            ],
        ]);

        $this->add([
            'type' => 'Zend\Form\Element\Text',
            'name' => 'keywords',
            'attributes' => [
                'required'   => false,
                'size'        => 40,
                'placeholder' => 'Keywords (max 15 words) seperate by commas',
            ],
            'options' => [
                'label' => 'Keywords',
            ],
        ]);

        $this->add([
            'type' => 'Zend\Form\Element\Text',
            'name' => 'description',
            'attributes' => [
                'required'   => false,
                'size'        => 40,
                'placeholder' => 'Description (max 150 characters)',
            ],
            'options' => [
                'label' => 'Description',
            ],
        ]);

        $this->add([
            'type' => 'Zend\Form\Element\Select',
            'name' => 'language',
            'options' => [
                'label' => 'Language',
                'empty_option' => 'Please choose a language',
            ],
        ]);

        $this->add([
            'type' => 'Zend\Form\Element\Select',
            'name' => 'parent',
            'options' => [
                'label' => 'Parent',
            ],
        ]);

        $valueOptions = [];
        $valueOptions[0] = "Main menu";
        $valueOptions[1] = "Left menu";
        $valueOptions[2] = "Right menu";
        $valueOptions[3] = "Footer menu";
        $this->add([
            'type' => 'Zend\Form\Element\Select',
            'name' => 'menutype',
            'options' => [
                'empty_option' => 'Please choose menu type',
                'value_options' => $valueOptions,
                'label' => 'Choose menu type',
            ],
        ]);

        $valueOptions = [];
        // 0 index missed intentionally
        $valueOptions[1] = "Column one";
        $valueOptions[2] = "Column two";
        $valueOptions[3] = "Column three";
        $valueOptions[4] = "Column four";
        $this->add([
            'type' => 'Zend\Form\Element\Select',
            'name' => 'footercolumn',
            'options' => [
                'empty_option' => 'Please choose footer column',
                'value_options' => $valueOptions,
                'label' => 'Choose footer column',
            ],
        ]);

        $this->add([
            'type' => 'Zend\Form\Element\Csrf',
            'name' => 's',
            'options' => [
                'csrf_options' => [
                    'timeout' => 1400,
                ],
            ],
        ]);

        $this->add([
            'name' => 'submit',
            'attributes' => [
                'type'  => 'submit',
                'id' => 'submitbutton',
            ],
        ]);

        $this->add([
            'type' => 'Zend\Form\Element\Hidden',
            'name' => 'id',
        ]);

        $this->add([
            'type' => 'Zend\Form\Element\Hidden',
            'name' => 'menulink',
            'attributes' => [
                'id' => 'menulink',
            ],
        ]);
    }

    public function getInputFilterSpecification()
    {
        return [
            [
                'name'     => 'id',
                'required' => false,
                'filters'  => [
                    ['name' => 'Int'],
                ],
            ],
            [
                "name"=>"caption",
                "required" => true,
                'filters' => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                ],
                'validators' => [
                    ['name' => 'NotEmpty'],
                    [
                        'name'    => 'StringLength',
                        'options' => [
                            'encoding' => 'UTF-8',
                            'min' => 1,
                            'max' => 200,
                        ],
                    ],
                ],
            ],
            [
                "name"=>"menuOrder",
                "required" => false,
                'filters'  => [
                    ['name' => 'Int'],
                ],
                'validators' => [
                    [
                        'name' => 'Regex',
                        'options' => [
                            'pattern' => '/^[0-9]+$/',
                        ],
                    ],
                ],
            ],
            [
                "name"=>"language",
                "required" => false,
                'filters'  => [
                    ['name' => 'Int'],
                ],
                'validators' => [
                    [
                        'name' => 'Regex',
                        'options' => [
                            'pattern' => '/^[0-9]+$/',
                        ],
                    ],
                ],
            ],
            [
                "name"=>"parent",
                "required" => false,
                'filters'  => [
                    ['name' => 'Int'],
                ],
                'validators' => [
                    [
                        'name' => 'Regex',
                        'options' => [
                            'pattern' => '/^[0-9]+$/',
                        ],
                    ],
                ],
            ],
            [
                "name"=>"keywords",
                "required" => false,
                'filters' => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                ],
                'validators' => [
                    [
                        'name'    => 'StringLength',
                        'options' => [
                            'encoding' => 'UTF-8',
                            'min' => 0,
                            'max' => 200,
                        ],
                    ],
                ],
            ],
            [
                "name"=>"description",
                "required" => false,
                'filters' => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                ],
                'validators' => [
                    [
                        'name'    => 'StringLength',
                        'options' => [
                            'encoding' => 'UTF-8',
                            'min' => 0,
                            'max' => 150,
                        ],
                    ],
                ],
            ],
            [
                "name"=>"menutype",
                "required" => false,
                'filters'  => [
                    ['name' => 'Int'],
                ],
                'validators' => [
                    [
                        'name' => 'Regex',
                        'options' => [
                            'pattern' => '/^[0-9]+$/',
                        ],
                    ],
                ],
            ],
            [
                "name"=>"footercolumn",
                "required" => false,
                'filters'  => [
                    ['name' => 'Int'],
                ],
                'validators' => [
                    [
                        'name' => 'Regex',
                        'options' => [
                            'pattern' => '/^[0-9]+$/',
                        ],
                    ],
                ],
            ],
            [
                "name"=>"menulink",
                "required" => false,
                'filters' => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                    ['name' => 'StringToLower'],
                ],
            ],
        ];
    }
}
