<?php
namespace Admin\Form;
use Zend\Form\Form;
use Zend\Form\Element;

class TermCategoryForm extends Form
{
    public function __construct($options = null)
    {
        parent::__construct("termCategory");

        $elements = array();

        $elements[0] = new Element\Text('name');
        $elements[0]->setLabel("Name");
        $elements[0]->setAttributes(array(
            'required'   => true,
            'size'        => 40,
            'class'      => 'term-name',
            'placeholder' => 'Name',
        ));
        if ($options != null) {
            $elements[0]->setValue($options->name);
        }

        $elements[1] = new Element\Submit('submit');
        $elements[1]->setAttributes(array(
            'id' => 'submitbutton',
            'class' => 'term-button',
        ));
        if ($options != null) {
            $elements[2] = new Element\Hidden('id');
            $elements[2]->setValue($options->id);
        }
        foreach ($elements as $e) {
            $this->add($e);
        }
    }
}
