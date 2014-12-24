<?php
namespace Application\Form;

use Zend\Form\Form;
use Zend\Form\Element;

class LoginForm extends Form
{
    public function __construct()
    {
        parent::__construct('loginform');

        $elements = array();

        $elements[0] = new Element\Email("email");
        $elements[0]->setAttributes(array(
            'required'    => true,
            'min'         => 3,
            'max'         => 20,
            'size'        => 30,
            'placeholder' => 'johnsmith@example.com',
        ));

        $elements[1] = new Element\Password("password");
        $elements[1]->setAttributes(array(
            'required'    => true,
            'size'        => 30,
            'placeholder' => '123456789',
        ));

        $elements[10] = new Element\Submit("login");
        $elements[10]->setAttributes(array(
            'id'    => 'submitbutton',
        ));

        foreach($elements as $e)
        {
            $this->add($e);
        }

        $inputFilter = new \Zend\InputFilter\InputFilter();
        $factory = new \Zend\InputFilter\Factory();

        $inputFilter->add($factory->createInput(array(
            "name"=>"email",
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
                        'min'      => 3,
                        'max'      => 25,
                    ),
                ),
                array('name' => 'NotEmpty'),
            ),
        )));
        $inputFilter->add($factory->createInput(array(
            "name"     =>"password",
            'required' => true,
            'filters'  => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
            ),
            'validators' => array(
                array(
                    'name' => 'StringLength',
                    'options' => array(
                        'encoding' => 'UTF-8',
                        'min' => 5,
                        'max' => 20,
                    ),
                ),
                array('name' => 'NotEmpty'),
            ),
        )));
    }
}