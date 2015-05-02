<?php
namespace Application\Form;

use Zend\Form\Form;
use Zend\Form\Element;
use Zend\Captcha;
use Zend\Captcha\Image as CaptchaImage;
class RegistrationForm extends Form
{
    public function __construct()
    {
        parent::__construct('registration');

        $elements = array();

        $elements[0] = new Element\Text("name");
        $elements[0]->setAttributes(array(
            'required'    => true,
            'min'         => 3,
            'max'         => 20,
            'size'        => 30,
        ));

        $elements[1] = new Element\Password("password");
        $elements[1]->setAttributes(array(
            'required'    => true,
            'size'        => 30,
            'min'         => 8,
            'placeholder' => '123456789',
        ));

        $elements[2] = new Element\Password("repeatpw");
        $elements[2]->setAttributes(array(
            'required'    => true,
            'size'        => 30,
            'placeholder' => '123456789',
        ));

        $elements[3] = new Element\Email("email");
        $elements[3]->setAttributes(array(
            'required'    => true,
            'size'        => 30,
            'min'         => 3,
            'placeholder' => 'johnsmith@example.com',
        ));

        $captchaImage = new CaptchaImage(array(
            'font'           => './data/fonts/arial.ttf',
            'width'          => 180,
            'height'         => 50,
            'size'           => 30,
            'fsize'          => 20,
            'dotNoiseLevel'  => 10,
            'lineNoiseLevel' => 2,
            )
        );

        $captchaImage->setImgDir('./public/userfiles/captcha');
        $captchaImage->setImgUrl('/userfiles/captcha');
        $elements[4] = new Element\Captcha('captcha');
        $elements[4]->setCaptcha($captchaImage);
        $elements[4]->setAttributes(array(
            'required'    => true,
            'size'        => 30,
            'class'       => 'captcha-input',
        ));

        $elements[8] = new Element\Csrf('s');
        $elements[20] = new Element\Submit("register");
        $elements[20]->setAttributes(array(
            'id'    => 'submitbutton',
        ));

        $inputFilter = new \Zend\InputFilter\InputFilter();
        $factory = new \Zend\InputFilter\Factory();
        $inputFilter->add($factory->createInput(array(
            "name"=>"email",
            'required' => true,
            'filters' => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
            ),
            "validators" => array(
                array(
                    'name' => 'EmailAddress',
                    'options' => array(
                        'encoding' => 'UTF-8',
                        'messages' => array('emailAddressInvalidFormat' => "Email address doesn't appear to be valid."),
                    ),
                ),
                array(
                    'name'    => 'StringLength',
                    'options' => array(
                        'encoding' => 'UTF-8',
                        'min'      => 5,
                    ),
                ),
                array('name' => 'NotEmpty'),
            ),
        )));
        $inputFilter->add($factory->createInput(array(
            "name"=>"name",
            'required' => true,
            'filters' => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
            ),
            'validators' => array(
                array(
                    'name' => 'StringLength',
                    'options' => array(
                        'encoding' => 'UTF-8',
                        'min' => 3,
                    ),
                ),
                array('name' => 'NotEmpty'),
            ),
        )));
        $inputFilter->add($factory->createInput(array(
            "name"=>"password",
            'required' => true,
            'filters' => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
            ),
            'validators' => array(
                array(
                    'name' => 'StringLength',
                    'options' => array(
                        'encoding' => 'UTF-8',
                        'min' => 8,
                    ),
                ),
                array('name' => 'NotEmpty'),
            ),
        )));
        $inputFilter->add($factory->createInput(array(
            'name' => 'repeatpw',
            'required' => true,
            'filters' => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
            ),
            'validators' => array(
                array(
                    'name' => 'StringLength',
                    'options' => array(
                        'encoding' => 'UTF-8',
                        'min' => 8,
                    ),
                ),
                array('name' => 'NotEmpty'),
                array(
                    'name' => 'Identical',
                    'options' => array(
                        'token' => 'password',
                        'message' => 'Passwords do not match',
                    ),
                ),
            ),
        )));
        $this->setInputFilter($inputFilter);

        foreach ($elements as $e) {
            $this->add($e);
        }
    }
}
