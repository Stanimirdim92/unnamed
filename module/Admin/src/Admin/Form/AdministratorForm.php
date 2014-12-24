<?php
namespace Admin\Form;
use Zend\Form\Form;
use Zend\Form\Element;
class AdministratorForm extends Form
{
    public function __construct($options = null)
    {
        parent::__construct("administrator");
        $elements = array();

        $elements[5] = new Element\Text('user');
        $elements[5]->setLabel('User ID');
        $elements[5]->setAttributes(array(
            'required'   => true,
            'size'        => 40,
            'class'      => 'administrator-user',
            'placeholder' => 'User ID',
        ));

        if($options!=null and $options->user)
            $elements[5]->setValue($options->user);

        $elements[111] = new Element\Submit('submit');
        $elements[111]->setAttribute('id', 'submitbutton');

        if($options!=null)
        {
            $elements[112] = new Element\Hidden('id');
            $elements[112]->setValue($options->id);
        }
        
        foreach($elements as $e)
        {
            $this->add($e);
        }
    }
}
