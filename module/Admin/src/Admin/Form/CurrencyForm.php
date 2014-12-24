<?php
namespace Admin\Form;
use Zend\Form\Form;
use Zend\Form\Element;
class CurrencyForm extends Form
{
    public function __construct($options = null)
    {
        parent::__construct("Currency");
        $elements = array();

        $elements[0] = new Element\Text('name');
        $elements[0]->setLabel('Name');
        $elements[0]->setAttributes(array(
            'required'   => true,
            'placeholder' => 'Name',
        ));
        if($options!=null and $options->name)
            $elements[0]->setValue($options->name);

        $elements[1] = new Element\Checkbox('active');
        $elements[1]->setLabel('Active');
        if($options!=null and $options->active)
            $elements[1]->setValue($options->active);

        $elements[2] = new Element\Text('symbol');
        $elements[2]->setLabel('Symbol name');
        $elements[2]->setAttributes(array(
            'required'   => true,
            'placeholder' => 'fa-dollar or fa-gbp etc.',
        ));
        if($options!=null and $options->symbol)
            $elements[2]->setValue($options->symbol);

        $elements[22] = new Element\Submit('submit');
        $elements[22]->setAttributes(array(
            'id' => 'submitbutton',
            'class' => 'Currency-button',
        ));

        if($options!=null)
        {
            $elements[33] = new Element\Hidden('id');
            $elements[33]->setValue($options->id);
        }

        foreach($elements as $e)
        {
            $this->add($e);
        }
    }
}
