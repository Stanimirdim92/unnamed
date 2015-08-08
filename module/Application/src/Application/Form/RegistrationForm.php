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

namespace Application\Form;

use Zend\Form\Form;
use Zend\Captcha;
use Zend\Captcha\Image as CaptchaImage;
use Zend\InputFilter\InputFilterProviderInterface;

class RegistrationForm extends Form implements InputFilterProviderInterface
{
    public function __construct()
    {
        parent::__construct('registration');
    }

    public function init()
    {
        $this->setAttribute('method', 'post');
        $this->setAttribute('action', '/registration/processregistration');

        $this->add([
            'type' => 'Zend\Form\Element\Text',
            'name' => 'name',
            'attributes' => [
                'required' => true,
                'min' => 3,
                'max' => 20,
                'size' => 30,
            ],
        ]);

        $this->add([
            'type' => 'Zend\Form\Element\Password',
            'name' => 'password',
            'attributes' => [
                'required' => true,
                'min' => 8,
                'size' => 30,
                'placeholder' => '1234567890',
            ],
        ]);

        $this->add([
            'type' => 'Zend\Form\Element\Password',
            'name' => 'repeatpw',
            'attributes' => [
                'required' => true,
                'size' => 30,
                'placeholder' => '1234567890',
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
        ]);

        $captchaImage = new CaptchaImage([
            'font'           => './data/fonts/arial.ttf',
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

        $this->add([
            'type' => 'Zend\Form\Element\Captcha',
            'name' => 'captcha',
            'attributes' => [
                'class' => 'captcha-input',
                'size' => 30,
            ],
            'options' => [
                'captcha' => $captchaImage,
            ],
        ]);

        $this->add([
            'type' => 'Zend\Form\Element\Csrf',
            'name' => 's',
            'options' => [
                'csrf_options' => [
                    'timeout' => 320,
                ],
            ],
        ]);

        $this->add([
            'name' => 'register',
            'attributes' => [
                'type'  => 'submit',
                'id' => 'submitbutton',
            ],
        ]);
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
                        ],
                    ],
                    ['name' => 'NotEmpty'],
                ],
            ],
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
