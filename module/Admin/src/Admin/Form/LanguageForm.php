<?php
namespace Admin\Form;
use Zend\Form\Form;
use Zend\Form\Element;
class LanguageForm extends Form
{
    public function __construct($options = null)
    {
        parent::__construct("language");
        $elements = array();

        $elements[0] = new Element\Text('name');
        $elements[0]->setLabel('Name');
        $elements[0]->setAttributes(array(
            'required'   => true,
            'class'      => 'language-name',
            'placeholder' => 'Name',
        ));
        if($options!=null and $options->name)
            $elements[0]->setValue($options->name);

        $elements[1] = new Element\Checkbox('active');
        $elements[1]->setLabel('Active');
        if($options!=null and $options->active)
            $elements[1]->setValue($options->active);

        $elements[2] = new Element\Submit('submit');
        $elements[2]->setAttributes(array(
            'id' => 'submitbutton',
            'class' => 'language-button',
        ));

        if($options!=null)
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
