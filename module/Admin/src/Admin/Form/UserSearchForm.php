<?php
namespace Admin\Form;
use Zend\Form\Form;
use Zend\Form\Element;
class UserSearchForm extends Form
{
    public function __construct($options = null)
    {
        parent::__construct("usersearch");
        $elements = array();
        $elements[0] = new Element\Text('search');
        $elements[0]->setLabel('Search')
                ->setAttributes(array(
                    'size' => 40,
                    'class' => "usersearch",
                    'autocomplete' => "off"
                ));
        $elements[1] = new Element\Submit('submit');
        $elements[1]->setAttribute('id', 'searchbutton');
        $elements[1]->setLabel('');
        foreach($elements as $e)
        {
          $this->add($e);
        }
    }
}
