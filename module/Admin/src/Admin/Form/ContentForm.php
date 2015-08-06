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
 * @version    0.0.4
 * @link       TBA
 */

namespace Admin\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;

class ContentForm extends Form implements InputFilterProviderInterface
{
    public function __construct()
    {
        parent::__construct("content");
    }

    public function init()
    {
        $this->setAttribute('method', 'post');

        /**
         * Specific image for this content
         */
        $this->add([
            'type' => 'Zend\Form\Element\File',
            'name' => 'preview',
            'attributes' => [
                'id' => 'preview',
                'class' => 'preview',
            ],
            'options' => [
                'label' => 'Image',
            ],
        ]);

        /**
         *Gallery for all contents
         */
        $this->add([
            'type' => 'Zend\Form\Element\File',
            'name' => 'imageUpload',
            'attributes' => [
                'id' => 'imgajax',
                'class' => 'imgupload',
                'multiple' => true,
            ],
            'options' => [
                'label' => 'Image',
            ],
        ]);

        $this->add([
            'type' => 'Zend\Form\Element\Text',
            'name' => 'title',
            'attributes' => [
                'required'   => true,
                'size'        => 40,
                'id'         => "seo-caption",
                'placeholder' => 'Title',
            ],
            'options' => [
                'label' => 'Title',
            ],
        ]);

        $this->add([
            'type' => 'Zend\Form\Element\Text',
            'name' => 'text',
            'attributes' => [
                'class'   => 'ckeditor',
                'rows'        => 5,
                'cols'      => 80,
            ],
            'options' => [
                'label' => 'Text',
            ],
        ]);

        $valueOptions = [];
        for ($i = 1; $i < 100; $i++) {
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
            'type' => 'Zend\Form\Element\Select',
            'name' => 'type',
            'options' => [
                'label' => 'Type',
                'empty_option' => 'Please choose your content type',
                'value_options' => [
                    '0' => "Menu",
                    '1' => "News",
                ],
            ],
        ]);

        $this->add([
            'type' => 'Zend\Form\Element\Text',
            'name' => 'date',
            'attributes' => [
                'size'  => 20,
            ],
            'options' => [
                'label' => 'Date',
            ],
        ]);

        $this->add([
            'type' => 'Zend\Form\Element\Select',
            'name' => 'menu',
            'options' => [
                'label' => 'Menu',
                'empty_option' => 'Please choose your menu',
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
            'type' => 'Zend\Form\Element\Csrf',
            'name' => 's',
            'options' => [
                'csrf_options' => [
                    'timeout' => 3600,
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
            'name' => 'titleLink',
            'attributes' => [
                'id' => 'titleLink',
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
                "name"=>"title",
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
                "name"=>"text",
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
                "name"=>"menu",
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
                "name"=>"type",
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
                "name"=>"date",
                "required" => false,
                'filters' => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                ],
            ],
            [
                "name"=>"preview",
                "required" => false,
                'validators' => [
                    [
                        'name' => 'Zend\Validator\File\Size',
                        'options' => [
                            'min' => '10kB',
                            'max' => '5MB',
                            'useByteString' => true,
                        ],
                    ],
                    [
                        'name' => 'Zend\Validator\File\Extension',
                        'options' => [
                            'extension' => [
                                'jpg',
                                'gif',
                                'png',
                                'jpeg',
                                'bmp',
                                'webp',
                            ],
                            'case' => true,
                        ],
                    ],
                ],
            ],
            [
                "name"=>"imageUpload",
                "required" => false,
                'validators' => [
                    [
                        'name' => 'Zend\Validator\File\Size',
                        'options' => [
                            'min' => '10kB',
                            'max' => '5MB',
                            'useByteString' => true,
                        ],
                    ],
                    [
                        'name' => 'Zend\Validator\File\Extension',
                        'options' => [
                            'extension' => [
                                'jpg',
                                'gif',
                                'png',
                                'jpeg',
                                'bmp',
                                'webp',
                            ],
                            'case' => true,
                        ],
                    ],
                ],
            ],
            [
                "name"=>"titleLink",
                "required" => true,
                'filters' => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                    ['name' => 'StringToLower'],
                ],
            ],
        ];
    }
}
