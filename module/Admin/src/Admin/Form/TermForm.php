<?php
namespace Admin\Form;
use Zend\Form\Form;
use Zend\Form\Element;

class TermForm extends Form
{

    public function __construct($options = null, $termcategories = array())
    {
        parent::__construct("term");

        $elements = array();

        $elements[0] = new Element\Text('name');
        $elements[0]->setLabel("Name");
        $elements[0]->setAttributes(array(
            'required'   => true,
            'size'        => 40,
            'class'      => 'term-name',
            'placeholder' => 'Name',
        ));
        if($options != null)
        {
            $elements[0]->setValue($options->name);
        }

        $elements[1] = new Element\Select('termcategory');
        $elements[1]->setLabel('TermCategory');
        $valueOptions = array();

        foreach($termcategories as $item)
        {
            $valueOptions[$item->id] = $item->toString();
        }
        $elements[1]->setValueOptions($valueOptions);
        if($options != null)
        {
            $elements[1]->setValue($options->termcategory);
        }

        $elements[2] = new Element\Submit('submit');
        $elements[2]->setAttributes(array(
            'id' => 'submitbutton',
            'class' => 'term-button',
        ));
        if($options != null)
        {
            $elements[3] = new Element\Hidden('id');
            $elements[3]->setValue($options->id);
        }

        foreach($elements as $e)
        {
            $this->add($e);
		}
    }
}
