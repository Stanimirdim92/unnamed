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

class AdminMenuForm extends Form implements InputFilterProviderInterface
{
    public function __construct()
    {
        parent::__construct("admin-menu");
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
            'class'      => 'admin-menu-caption',
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
            'type' => 'Zend\Form\Element\Checkbox',
            'name' => 'advanced',
            'options' => [
                'label' => 'Advanced',
            ],
        ]);

        $this->add([
            'type' => 'Zend\Form\Element\Text',
            'name' => 'controller',
            'attributes' => [
                'required'   => false,
                'size'        => 40,
                'class'       => 'admin-menu-controller',
                'placeholder' => 'Controller',
            ],
            'options' => [
                'label' => 'Controller',
            ],
        ]);

        $this->add([
            'type' => 'Zend\Form\Element\Text',
            'name' => 'class',
            'attributes' => [
                'required'   => false,
                'size'        => 40,
                'class'       => 'admin-menu-class',
                'placeholder' => 'Class',
            ],
            'options' => [
                'label' => 'Class',
            ],
        ]);

        $this->add([
            'type' => 'Zend\Form\Element\Text',
            'name' => 'action',
            'attributes' => [
                'required'   => false,
                'size'        => 40,
                'class'       => 'admin-menu-action',
                'placeholder' => 'Action',
            ],
            'options' => [
                'label' => 'Action',
            ],
        ]);

        $this->add([
            'type' => 'Zend\Form\Element\Text',
            'name' => 'description',
            'attributes' => [
                'required'   => false,
                'size'        => 40,
                'class'       => 'admin-menu-description',
                'placeholder' => 'Description',
            ],
            'options' => [
                'label' => 'Description',
            ],
        ]);

        $this->add([
            'type' => 'Zend\Form\Element\Select',
            'name' => 'parent',
            'options' => [
                'label' => 'Parent',
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
    }

    public function getInputFilterSpecification()
    {
        return [
            [
                'name' => 'id',
                'required' => false,
                'filters' => [
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
                "name"=>"advanced",
                "required" => false,
                'filters'  => [
                    ['name' => 'Int'],
                ],
                'validators' => [
                    [
                        'name' => 'Regex',
                        'options' => [
                            'pattern' => '/^[0-1]+$/',
                        ],
                    ],
                ],
            ],
            [
                "name"=>"controller",
                "required" => false,
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
                "name"=>"action",
                "required" => false,
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
                "name"=>"class",
                "required" => false,
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
                            'max' => 50,
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
        ];
    }
}
