<?php
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
    protected $elements = array();

    public function __construct()
    {
        parent::__construct('contact_form');

        $this->elements[0] = new Element\Text('name');
        $this->elements[0]->setAttributes(array(
            'required'    => true,
            'min'         => 3,
            'max'         => 20,
            'size'        => 30,
        ));

        $this->elements[1] = new Element\Text('email');
        $this->elements[1]->setAttributes(array(
            'required'    => true,
            'min'         => 5,
            'size'        => 30,
            'placeholder' => 'johnsmith@example.com',
        ));

        $this->elements[2] = new Element\Text('subject');
        $this->elements[2]->setAttributes(array(
            'required'    => true,
            'min'         => 3,
            'size'        => 30,
        ));

        $this->elements[4] = new Element\Textarea('message');
        $this->elements[4]->setAttributes(array(
            'required'     => true,
            'rows'         => 8,
            'cols'         => 70,
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
        $this->elements[3] = new Element\Captcha('captcha');
        $this->elements[3]->setCaptcha($captchaImage);
        $this->elements[3]->setAttributes(array(
            'required'    => true,
            'size'        => 30,
            'class'       => 'captcha-input',
        ));

        $this->elements[5] = new Element\Submit('submit');
        $this->elements[5]->setAttributes(array(
            'id'    => 'submitbutton',
        ));
        
        $this->elements[8] = new Element\Csrf('s');

        $this->inputFilter = new \Zend\InputFilter\InputFilter();
        $factory = new \Zend\InputFilter\Factory();
        $this->inputFilter->add($factory->createInput(array(
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
        $this->inputFilter->add($factory->createInput(array(
            "name"=>"subject",
            'required' => true,
            'filters'  => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
            ),
            'validators' => array(
                array(
                    'name'    => 'StringLength',
                    'options' => array(
                        'encoding' => 'UTF-8',
                        'min'      => 3,
                        'max'      => 100,
                    ),
                ),
                array('name' => 'NotEmpty'),
            ),
        )));
        $this->inputFilter->add($factory->createInput(array(
            "name"=>"message",
            'required' => true,
            'filters'  => array(
                array('name' => 'StripTags'),
            ),
            'validators' => array(
                array(
                    'name'    => 'StringLength',
                    'options' => array(
                        'encoding' => 'UTF-8',
                        'min'      => 3,
                        'max'      => 3000,
                    ),
                ),
                array('name' => 'NotEmpty'),
            ),
        )));
        // $this->inputFilter->add($factory->createInput(array(
        //     "name"=>"captcha",
        //     'required' => true,
        //     'filters' => array(
        //         array('name' => 'StripTags'),
        //         array('name' => 'StringTrim'),
        //     ),
        //     'validators' => array(
        //         array(
        //             'name' => 'StringLength',
        //             'options' => array(
        //                 'encoding' => 'UTF-8',
        //                 'min' => 3,
        //                 'max' => 30,
        //             ),
        //         ),
        //         array('name' => 'NotEmpty'),
        //     ),
        // )));
        $this->inputFilter->add($factory->createInput(array(
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
                        'max' => 20,
                    ),
                ),
                array('name' => 'NotEmpty'),
            ),
        )));
        $this->setInputFilter($this->inputFilter);
        foreach($this->elements as $e)
        {
            $this->add($e);
        }
    }
}
