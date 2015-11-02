<?php

/**
 * @copyright  2015 (c) Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 *
 * @version    0.0.21
 *
 * @link       TBA
 */

namespace Application\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;

final class NewPasswordForm extends Form implements InputFilterProviderInterface
{
    public function __construct()
    {
        parent::__construct('resetpw');
    }

    public function init()
    {
        $this->setAttribute('method', 'post');
        $this->setAttribute('action', '/login/newpasswordprocess');
        $this->setAttribute('role', 'form');

        $this->add(
            [
            'type' => 'Zend\Form\Element\Password',
            'name' => 'password',
            'attributes' => [
                'required' => true,
                'min' => 8,
                'size' => 30,
                'placeholder' => '1234567890',
            ],
            'options' => [
                'label' => "Password",
            ],
            ]
        );

        $this->add(
            [
            'type' => 'Zend\Form\Element\Password',
            'name' => 'repeatpw',
            'attributes' => [
                'required' => true,
                'size' => 30,
                'placeholder' => '1234567890',
            ],
            'options' => [
                'label' => "Repeat password",
            ],
            ]
        );

        $this->add(
            [
            'type' => 'Zend\Form\Element\Csrf',
            'name' => 's',
            'options' => [
                'csrf_options' => [
                    'timeout' => 320,
                ],
            ],
            ]
        );

        $this->add(
            [
            'name' => 'resetpw',
            'attributes' => [
                'type'  => 'submit',
                'id' => 'submitbutton',
            ],
            ]
        );
    }

    public function getInputFilterSpecification()
    {
        return [
            [
                "name"=>"password",
                'required' => true,
                'filters' => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                ],
                'validators' => [
                    [
                        'name' => 'StringLength',
                        'options' => [
                            'encoding' => 'UTF-8',
                            'min' => 8,
                        ],
                    ],
                    ['name' => 'NotEmpty'],
                ],
            ],
            [
                'name' => 'repeatpw',
                'required' => true,
                'filters' => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                ],
                'validators' => [
                    [
                        'name' => 'StringLength',
                        'options' => [
                            'encoding' => 'UTF-8',
                            'min' => 8,
                        ],
                    ],
                    ['name' => 'NotEmpty'],
                    [
                        'name' => 'Identical',
                        'options' => [
                            'token' => 'password',
                            'message' => 'Passwords do not match',
                        ],
                    ],
                ],
            ],
        ];
    }
}
