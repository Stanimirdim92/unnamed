<?php

/**
 * @copyright  2015 (c) Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 *
 * @version    0.0.21
 *
 * @link       TBA
 */

namespace Admin\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;
use Doctrine\ORM\EntityManager;

final class UserForm extends Form implements InputFilterProviderInterface
{
    /**
     * @var Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     * @param $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        parent::__construct("user");
        $this->entityManager = $entityManager;
    }

    public function init()
    {
        $this->setAttribute('method', 'post');
        $this->setAttribute('role', 'form');

        $this->add(
            [
            'type' => 'Zend\Form\Element\Text',
            'name' => 'name',
            'options' => [
                'label' => 'Name',
                'object_manager' => $this->entityManager,
                'target_class' => 'Admin\Entity\User',
                'property' => "name",
            ],
            'attributes' => [
                'required'   => "true",
                'size'        => "40",
                'placeholder' => 'Name',
            ],
            ]
        );

        $this->add(
            [
            'type' => 'Zend\Form\Element\Text',
            'name' => 'surname',
            'options' => [
                'label' => 'Surname',
                'object_manager' => $this->entityManager,
                'target_class' => 'Admin\Entity\User',
                'property' => "surname",
            ],
            'attributes' => [
                'size'        => "40",
                'placeholder' => 'Surname',
            ],
            ]
        );

        $this->add(
            [
            'type' => 'Zend\Form\Element\Email',
            'name' => 'email',
            'options' => [
                'label' => 'Email',
                'object_manager' => $this->entityManager,
                'target_class' => 'Admin\Entity\User',
                'property' => "email",
            ],
            'attributes' => [
                'required' => "true",
                'min' => "3",
                'size' => "30",
                'placeholder' => 'johnsmith@example.com',
            ],
            ]
        );

        $this->add(
            [
            'type' => 'Zend\Form\Element\Text',
            'name' => 'birthDate',
            'options' => [
                'label' => 'Birthdate',
                'object_manager' => $this->entityManager,
                'target_class' => 'Admin\Entity\User',
                'property' => "birthDate",
            ],
            'attributes' => [
                'size'        => "40",
                'class'       => 'datetimepicker',
                'placeholder' => 'YYYY-MM-DD',
            ],
            ]
        );

        $this->add(
            [
            'type' => 'Zend\Form\Element\Checkbox',
            'name' => 'isDisabled',
            'options' => [
                'label' => 'Disabled',
                'object_manager' => $this->entityManager,
                'target_class' => 'Admin\Entity\User',
                'property' => "isDisabled",
            ],
            ]
        );

        $this->add(
            [
            'type' => 'Zend\Form\Element\Checkbox',
            'name' => 'hideEmail',
            'options' => [
                'label' => 'Hide email',
                'object_manager' => $this->entityManager,
                'target_class' => 'Admin\Entity\User',
                'property' => "hideEmail",
            ],
            ]
        );

        $this->add(
            [
            'type' => 'Zend\Form\Element\Csrf',
            'name' => 's',
            'options' => [
                'csrf_options' => [
                    'timeout' => 1400,
                ],
            ],
            ]
        );

        $this->add(
            [
            'name' => 'submit',
            'attributes' => [
                'type'  => 'submit',
                'id' => 'submitbutton',
                'value' => 'Save',
            ],
            ]
        );

        $this->add(
            [
            'type' => 'Zend\Form\Element\Hidden',
            'name' => 'id',
            'options' => [
                'object_manager' => $this->entityManager,
                'target_class' => 'Admin\Entity\User',
                'property' => "id",
            ],
            ]
        );
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
                "name"=>"isDisabled",
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
