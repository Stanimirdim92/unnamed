<?php
namespace Admin\Form;
use Zend\Form\Form;
use Zend\Form\Element;
class AjaxSearchForm extends Form
{
    public function __construct($options = null)
    {
        parent::__construct("search");
        $elements = [];
        $elements[0] = new Element\Text('search');
        $elements[0]->setLabel('Search')
                ->setAttributes([
                    'size' => 40,
                    'class' => "search",
                    'autocomplete' => "off",
                ]);
        foreach ($elements as $e) {
            $this->add($e);
        }
    }
}
