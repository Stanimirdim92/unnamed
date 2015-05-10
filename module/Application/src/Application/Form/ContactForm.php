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
 * @category   Application\ContactForm
 * @package    ZendPress
 * @author     Stanimir Dimitrov <stanimirdim92@gmail.com>
 * @copyright  2015 Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 * @version    0.03
 * @link       TBA
 */
namespace Application\Form;

use Zend\Form\Element;
use Zend\Form\Form;
use Zend\Captcha;
use Zend\Captcha\Image as CaptchaImage;
use Zend\InputFilter;

class ContactForm extends Form
{
    /**
     * @var null $inputFilter
     */
    protected $inputFilter = null;

    /**
     * @var array $elements
     */
    protected $elements = [];

    public function __construct()
    {
        parent::__construct('contact_form');

        $this->elements[0] = new Element\Text('name');
        $this->elements[0]->setAttributes([
            'required'    => true,
            'min'         => 3,
            'max'         => 20,
            'size'        => 30,
        ]);

        $this->elements[1] = new Element\Text('email');
        $this->elements[1]->setAttributes([
            'required'    => true,
            'min'         => 5,
            'size'        => 30,
            'placeholder' => 'johnsmith@example.com',
        ]);

        $this->elements[2] = new Element\Text('subject');
        $this->elements[2]->setAttributes([
            'required'    => true,
            'min'         => 3,
            'size'        => 30,
        ]);

        $this->elements[4] = new Element\Textarea('message');
        $this->elements[4]->setAttributes([
            'required'     => true,
            'rows'         => 8,
            'cols'         => 70,
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
        $this->elements[3] = new Element\Captcha('captcha');
        $this->elements[3]->setCaptcha($captchaImage);
        $this->elements[3]->setAttributes([
            'required'    => true,
            'size'        => 30,
            'class'       => 'captcha-input',
        ]);

        $this->elements[5] = new Element\Submit('submit');
        $this->elements[5]->setAttributes([
            'id'    => 'submitbutton',
        ]);

        $this->elements[8] = new Element\Csrf('s');

        $this->inputFilter = new \Zend\InputFilter\InputFilter();
        $factory = new \Zend\InputFilter\Factory();
        $this->inputFilter->add($factory->createInput([
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
        ]));
        $this->inputFilter->add($factory->createInput([
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
        ]));
        $this->inputFilter->add($factory->createInput([
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
        ]));
        $this->inputFilter->add($factory->createInput([
            "name"=>"captcha",
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
        ]));
        $this->inputFilter->add($factory->createInput([
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
                        'max' => 20,
                    ],
                ],
                ['name' => 'NotEmpty'],
            ],
        ]));
        $this->setInputFilter($this->inputFilter);
        foreach ($this->elements as $e) {
            $this->add($e);
        }
    }
}
