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

final class SettingsMailForm extends Form implements InputFilterProviderInterface
{
    /**
     * @var array
     */
    private $config = [];

    /**
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->config = $config;
        parent::__construct("settings-mail");
    }

    public function init()
    {
        $this->setAttribute('method', 'post');
        $this->setAttribute('role', 'form');

        $this->add(
            [
            'type' => 'Zend\Form\Element\Text',
            'name' => 'host',
            'attributes' => [
                'required'   => true,
                'size'        => 40,
                'class'      => 'settings_host',
                'placeholder' => 'Host',
                'value' => $this->config['host'],
            ],
            'options' => [
                'label' => 'Host',
            ],
            ]
        );

        $this->add(
            [
            'type' => 'Zend\Form\Element\Text',
            'name' => 'name',
            'attributes' => [
                'required'   => true,
                'size'        => 40,
                'class'       => 'settings_name',
                'placeholder' => 'Name',
                'value' => $this->config['name'],
            ],
            'options' => [
                'label' => 'Name',
            ],
            ]
        );

        $this->add(
            [
            'type' => 'Zend\Form\Element\Text',
            'name' => 'port',
            'attributes' => [
                'required'   => true,
                'size'        => 40,
                'class'       => 'settings_port',
                'placeholder' => 'Port',
                'value' => $this->config['port'],
            ],
            'options' => [
                'label' => 'Port',
            ],
            ]
        );

        $this->add(
            [
            'type' => 'Zend\Form\Element\Text',
            'name' => 'username',
            'attributes' => [
                'required'   => true,
                'size'        => 40,
                'class'       => 'settings_username',
                'placeholder' => 'Username',
                'value' => $this->config['username'],
            ],
            'options' => [
                'label' => 'Username',
            ],
            ]
        );

        $this->add(
            [
            'type' => 'Zend\Form\Element\Text',
            'name' => 'password',
            'attributes' => [
                'required'   => true,
                'size'        => 40,
                'class'       => 'settings_password',
                'placeholder' => 'Password',
                'value' => $this->config['password'],
            ],
            'options' => [
                'label' => 'Password',
            ],
            ]
        );

        $this->add(
            [
            'type' => 'Zend\Form\Element\Text',
            'name' => 'ssl',
            'attributes' => [
                'required'   => true,
                'size'        => 40,
                'class'       => 'settings_ssl',
                'placeholder' => 'Certificate type',
                'value' => $this->config['ssl'],
            ],
            'options' => [
                'label' => 'Cert type',
            ],
            ]
        );

        $this->add(
            [
            'type' => 'Zend\Form\Element\Email',
            'name' => 'from',
            'attributes' => [
                'required'   => true,
                'size'        => 40,
                'class'       => 'settings_from',
                'placeholder' => 'Admin email',
                'value' => $this->config['from'],
            ],
            'options' => [
                'label' => 'Admin email',
            ],
            ]
        );

        $this->add(
            [
            'type' => 'Zend\Form\Element\Select',
            'name' => 'connection_class',
            'attributes' => [
                'required'   => true,
                'class'       => 'settings_connection_class',
                'value' => $this->config['connection_class'],
            ],
            'options' => [
                'label' => 'Connection type',
                'value_options' => $this->config['connection_classes'],
            ],
            ]
        );

        $this->add(
            [
            'type' => 'Zend\Form\Element\Csrf',
            'name' => 's',
            'options' => [
                'csrf_options' => [
                    'timeout' => 3600,
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
            ],
            ]
        );
    }

    public function getInputFilterSpecification()
    {
        return [
            [
                "name" => "host",
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
                            'max' => 255,
                        ],
                    ],
                ],
            ],
            [
                "name" => "name",
                "required" => true,
                'filters' => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                ],
                'validators' => [
                    [
                        'name'    => 'StringLength',
                        'options' => [
                            'encoding' => 'UTF-8',
                            'min' => 1,
                            'max' => 255,
                        ],
                    ],
                ],
            ],
            [
                "name" => "port",
                "required" => false,
                'filters' => [
                    ['name' => 'Int'],
                ],
            ],
            [
                "name" => "from",
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
                "name" => "connection_class",
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
                            'max' => 255,
                        ],
                    ],
                ],
            ],
            [
                "name" => "username",
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
                            'max' => 255,
                        ],
                    ],
                ],
            ],
            [
                "name" => "password",
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
                            'max' => 255,
                        ],
                    ],
                ],
            ],
            [
                "name" => "ssl",
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
                            'max' => 255,
                        ],
                    ],
                ],
            ],
        ];
    }
}
