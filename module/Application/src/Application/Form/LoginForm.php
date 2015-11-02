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

final class LoginForm extends Form implements InputFilterProviderInterface
{
    public function __construct()
    {
        parent::__construct('loginform');
    }

    public function init()
    {
        $this->setAttribute('method', 'post');
        $this->setAttribute('action', '/login/processlogin');
        $this->setAttribute('role', 'form');

        $this->add(
            [
            'type' => 'Zend\Form\Element\Email',
            'name' => 'email',
            'attributes' => [
                'required' => true,
                'min' => 3,
                'size' => 30,
                'placeholder' => 'johnsmith@example.com',
            ],
            ]
        );

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
            ]
        );

        $this->add(
            [
            'type' => 'Zend\Form\Element\Csrf',
            'name' => 's',
            'options' => [
                'csrf_options' => [
                    'timeout' => 600,
                ],
            ],
            ]
        );

        $this->add(
            [
            'name' => 'login',
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
                "name"     =>"password",
                'required' => true,
                'filters'  => [
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
        ];
    }
}
