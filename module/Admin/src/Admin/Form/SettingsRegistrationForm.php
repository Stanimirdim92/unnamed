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

final class SettingsRegistrationForm extends Form implements InputFilterProviderInterface
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
        parent::__construct("settings-registration");
    }

    public function init()
    {
        $this->setAttribute('method', 'post');
        $this->setAttribute('role', 'form');

        $this->add(
            [
            'type' => 'Zend\Form\Element\Checkbox',
            'name' => 'allow_registrations',
            'attributes' => [
                'class'      => 'settings_allow_registrations',
                'value' => $this->config['allow_registrations'],
            ],
            'options' => [
                'label' => 'Allow user registration',
            ],
            ]
        );

        $this->add(
            [
            'type' => 'Zend\Form\Element\Select',
            'name' => 'email_verification',
            'attributes' => [
                'class'       => 'settings_email_verification',
                'value' => $this->config['email_verification'],
            ],
            'options' => [
                'label' => 'Email verification type',
                'value_options' => $this->config['email_verifications'],
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
                "name" => "allow_registrations",
                "required" => false,
                'filters' => [
                    ['name' => 'Int'],
                ],
            ],
            [
                "name" => "email_verification",
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
                        ],
                    ],
                ],
            ],
        ];
    }
}
