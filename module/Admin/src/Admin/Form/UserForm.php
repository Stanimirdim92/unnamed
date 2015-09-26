<?php

/**
 * @copyright  2015 (c) Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 *
 * @version    0.0.14
 *
 * @link       TBA
 */

namespace Admin\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;

class UserForm extends Form implements InputFilterProviderInterface
{
    public function __construct()
    {
        parent::__construct("user");
    }

    public function init()
    {
        $this->setAttribute('method', 'post');

        $this->add([
            'type' => 'Zend\Form\Element\Text',
            'name' => 'name',
            'attributes' => [
                'required'   => true,
                'size'        => 40,
                'placeholder' => 'Name',
            ],
            'options' => [
                'label' => 'Name',
            ],
        ]);

        $this->add([
            'type' => 'Zend\Form\Element\Text',
            'name' => 'surname',
            'attributes' => [
                'required'   => false,
                'size'        => 40,
                'placeholder' => 'Surname',
            ],
            'options' => [
                'label' => 'Surname',
            ],
        ]);

        $this->add([
            'type' => 'Zend\Form\Element\Email',
            'name' => 'email',
            'attributes' => [
                'required' => true,
                'min' => 3,
                'size' => 30,
                'placeholder' => 'johnsmith@example.com',
            ],
            'options' => [
                'label' => 'Email',
            ],
        ]);

        $this->add([
            'type' => 'Zend\Form\Element\Text',
            'name' => 'birthDate',
            'attributes' => [
                'required'    => false,
                'size'        => 40,
                'class'       => 'datetimepicker',
                'placeholder' => 'YYYY-MM-DD',
            ],
            'options' => [
                'label' => 'Birthdate',
            ],
        ]);

        $this->add([
            'type' => 'Zend\Form\Element\Checkbox',
            'name' => 'deleted',
            'options' => [
                'label' => 'Disabled',
            ],
        ]);

        $this->add([
            'type' => 'Zend\Form\Element\Checkbox',
            'name' => 'hideEmail',
            'options' => [
                'label' => 'Hide email',
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
                'name'     => 'id',
                'required' => false,
                'filters'  => [
                    ['name' => 'Int'],
                ],
            ],
            [
                "name"=>"name",
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
                            'max' => 100,
                        ],
                    ],
                ],
            ],
            [
                "name"=>"surname",
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
                            'max' => 100,
                        ],
                    ],
                ],
            ],
            [
                "name"=>"email",
                'required' => true,
                'filters' => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                ],
                "validators" => [
                    [
                        'name' => 'EmailAddress',
                        'options' => [
                            'encoding' => 'UTF-8',
                            'messages' => ['emailAddressInvalidFormat' => "Email address doesn't appear to be valid."],
                        ],
                    ],
                    [
                        'name'    => 'StringLength',
                        'options' => [
                            'encoding' => 'UTF-8',
                            'min'      => 5,
                        ],
                    ],
                    ['name' => 'NotEmpty'],
                ],
            ],
            [
                "name"=>"birthDate",
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
                            'max' => 100,
                        ],
                    ],
                ],
            ],
            [
                "name"=>"deleted",
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
            // [
            //     "name"=>"image",
            //     "required" => false,
            //     'validators' => [
            //         [
            //             'name' => 'Zend\Validator\File\Size',
            //             'options' => [
            //                 'min' => '10kB',
            //                 'max' => '5MB',
            //                 'useByteString' => true,
            //             ],
            //         ],
            //         [
            //             'name' => 'Zend\Validator\File\Extension',
            //             'options' => [
            //                 'extension' => [
            //                     'jpg',
            //                     'gif',
            //                     'png',
            //                     'jpeg',
            //                     'bmp',
            //                     'webp',
            //                 ],
            //                 'case' => true,
            //             ],
            //         ],
            //     ],
            // ],
            [
                "name"=>"hideEmail",
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
        ];
    }
}
