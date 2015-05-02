<?php
namespace Application\Form;

use Zend\Form\Form;
use Zend\Form\Element;
use Zend\Captcha;
use Zend\Captcha\Image as CaptchaImage;

class ResetPasswordForm extends Form
{
    public function __construct()
    {
        parent::__construct('loginform');

        $elements = array();

        $elements[0] = new Element\Email("email");
        $elements[0]->setAttributes(array(
            'required'    => true,
            'min'         => 5,
            'size'        => 30,
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
        $elements[10] = new Element\Submit("resetpw");
        $elements[10]->setAttributes(array(
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
                        'max'      => 255,
                    ),
                ),
                array('name' => 'NotEmpty'),
            ),
        )));
        $this->setInputFilter($inputFilter);

        foreach ($elements as $e) {
            $this->add($e);
        }
    }
}
