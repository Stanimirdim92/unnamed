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
 * @category   Application\Login
 * @package    ZendPress
 * @author     Stanimir Dimitrov <stanimirdim92@gmail.com>
 * @copyright  2015 Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 * @version    0.03
 * @link       TBA
 */
namespace Application\Form;

use Zend\Form\Form;
use Zend\Form\Element;

class LoginForm extends Form
{
    public function __construct()
    {
        parent::__construct('loginform');

        $elements = [];

        $elements[0] = new Element\Email("email");
        $elements[0]->setAttributes([
            'required'    => true,
            'min'         => 5,
            'size'        => 30,
            'placeholder' => 'johnsmith@example.com',
        ]);

        $elements[1] = new Element\Password("password");
        $elements[1]->setAttributes([
            'required'    => true,
            'min'         => 8,
            'size'        => 30,
            'placeholder' => '123456789',
        ]);

        // $elements[8] = new Element\Csrf('s');
        $elements[10] = new Element\Submit("login");
        $elements[10]->setAttributes([
            'id'    => 'submitbutton',
        ]);

        $inputFilter = new \Zend\InputFilter\InputFilter();
        $factory = new \Zend\InputFilter\Factory();

        $inputFilter->add($factory->createInput([
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
        $inputFilter->add($factory->createInput([
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
        ]));
        $this->setInputFilter($inputFilter);

        foreach ($elements as $e) {
            $this->add($e);
        }
    }
}
