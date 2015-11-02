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
use Zend\Captcha;
use Zend\Captcha\Image as CaptchaImage;
use Zend\InputFilter\InputFilterProviderInterface;

final class ContactForm extends Form implements InputFilterProviderInterface
{
    public function __construct()
    {
        parent::__construct('contact_form');
    }

    public function init()
    {
        $this->setAttribute('method', 'post');
        $this->setAttribute('role', 'form');

        $this->add(
            [
            'type' => 'Zend\Form\Element\Text',
            'name' => 'name',
            'attributes' => [
                'required' => true,
                'min' => 3,
                'max' => 30,
                'size' => 30,
            ],
            ]
        );

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
            'type' => 'Zend\Form\Element\Text',
            'name' => 'subject',
            'attributes' => [
                'required' => true,
                'min' => 3,
                'size' => 30,
            ],
            ]
        );

        $this->add(
            [
            'type' => 'Zend\Form\Element\Textarea',
            'name' => 'message',
            'attributes' => [
                'required' => true,
                'rows' => 8,
                'cols' => 70,
            ],
            ]
        );


        $captchaImage = new CaptchaImage(
            [
            'font'           => 'public/layouts/default/front/fonts/arial.ttf',
            'width'          => 180,
            'height'         => 50,
            'size'           => 30,
            'fsize'          => 20,
            'dotNoiseLevel'  => 10,
            'lineNoiseLevel' => 2,
            ]
        );
        $captchaImage->setImgDir('./public/userfiles/captcha');
        $captchaImage->setImgUrl('/userfiles/captcha');

        $this->add(
            [
            'type' => 'Zend\Form\Element\Captcha',
            'name' => 'captcha',
            'attributes' => [
                'class' => 'captcha-input',
                'size' => 30,
            ],
            'options' => [
                'captcha' => $captchaImage,
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
                "name"=>"subject",
                'required' => true,
                'filters'  => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                ],
                'validators' => [
                    [
                        'name'    => 'StringLength',
                        'options' => [
                            'encoding' => 'UTF-8',
                            'min'      => 3,
                            'max'      => 100,
                        ],
                    ],
                    ['name' => 'NotEmpty'],
                ],
            ],
            [
                "name"=>"message",
                'required' => true,
                'filters'  => [
                    ['name' => 'StripTags'],
                ],
                'validators' => [
                    [
                        'name'    => 'StringLength',
                        'options' => [
                            'encoding' => 'UTF-8',
                            'min'      => 3,
                            'max'      => 3000,
                        ],
                    ],
                    ['name' => 'NotEmpty'],
                ],
            ],
            [
                "name"=>"name",
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
                            'min' => 3,
                            'max' => 30,
                        ],
                    ],
                    ['name' => 'NotEmpty'],
                ],
            ],
        ];
    }
}
