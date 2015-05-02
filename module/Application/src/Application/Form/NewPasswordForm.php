<?php
namespace Application\Form;

use Zend\Form\Form;
use Zend\Form\Element;

class NewPasswordForm extends Form
{
    public function __construct()
    {
        parent::__construct('resetpw');

        $elements = array();

        $elements[1] = new Element\Password("password");
        $elements[1]->setAttributes(array(
            'required'    => true,
            'min'         => 8,
            'size'        => 30,
        ));

        $elements[2] = new Element\Password("repeatpw");
        $elements[2]->setAttributes(array(
            'required'    => true,
            'min'         => 8,
            'size'        => 30,
        ));

        $elements[8] = new Element\Csrf('s');
        $elements[20] = new Element\Submit("resetpw");
        $elements[20]->setAttributes(array(
            'id'    => 'submitbutton',
        ));

        $inputFilter = new \Zend\InputFilter\InputFilter();
        $factory = new \Zend\InputFilter\Factory();
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
