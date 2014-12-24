<?php
namespace Admin\Form;

use Zend\Form\Form;
use Zend\Form\Element;

class MenuForm extends Form
{
    public function __construct($options = null, $languages = array(), $parents = array())
    {
        parent::__construct("menu");
        $elements = array();

        $elements[0] = new Element\Text('caption');
        $elements[0]->setLabel('Caption');
        $elements[0]->setAttributes(array(
            'required'   => true,
            'size'        => 40,
            'placeholder' => 'Caption',
        ));

        if($options!=null and $options->caption)
            $elements[0]->setValue($options->caption);

        $elements[1] = new Element\Select('menuOrder');
        $elements[1]->setLabel('Menu order');
        $valueOptions = array();
        for($i = 1; $i<40; $i++)
        {
              $valueOptions[$i] = $i;
        }
        $elements[1]->setValueOptions($valueOptions);
        if($options!=null and $options->menuOrder)
            $elements[1]->setValue($options->menuOrder);
        else
            $elements[1]->setValue(0);

        $elements[2] = new Element\Text('keywords');
        $elements[2]->setLabel('Keywords');
        $elements[2]->setAttributes(array(
            'required'   => false,
            'size'        => 40,
            'placeholder' => 'Keywords',
        ));
        if($options!=null and $options->keywords)
            $elements[2]->setValue($options->keywords);

        $elements[3] = new Element\Text('description');
        $elements[3]->setLabel('Description');
        $elements[3]->setAttributes(array(
            'required'   => false,
            'size'        => 40,
            'placeholder' => 'Description',
        ));
        if($options!=null and $options->description)
            $elements[3]->setValue($options->description);

        $elements[4] = new Element\Select('language');
        $elements[4]->setLabel('language');
        $valueOptions = array();
        
        foreach($languages as $item)
        {
            $valueOptions[$item->id] = $item->toString();
        }
        $elements[4]->setValueOptions($valueOptions);
        if($options!=null and $options->language)
        {
            $elements[4]->setValue($options->language);
        }

        $elements[5] = new Element\Select('parent');
        $elements[5]->setLabel('parent');
        $valueOptions = array();

        $valueOptions[0] = 'Select a parent';        
        foreach($parents as $item)
        {
            $valueOptions[$item->id] = $item->toString();
        }
        $elements[5]->setValueOptions($valueOptions);
        if($options!=null and $options->parent)
        {
            $elements[5]->setValue($options->parent);
        }

        $elements[6] = new Element\Select('menutype');
        $elements[6]->setLabel('Choose menu type')->setLabelAttributes(array("style" => "width: 200px;display: inline-block;"));
        $valueOptions = array();
        $valueOptions[0] = "As main menu";
        $valueOptions[1] = "As category menu";
        $valueOptions[2] = "As SEO menu (next to category menu)";
        $valueOptions[3] = "As footer menu";
        $elements[6]->setValueOptions($valueOptions)->setAttribute("id", "menutype");
        if($options!=null and $options->menutype)
            $elements[6]->setValue($options->menutype);

        $elements[7] = new Element\Select('footercolumn');
        $elements[7]->setLabel('Choose footer column')->setLabelAttributes(array("id" => "footercolumn"));
        $valueOptions = array();
        $valueOptions[1] = "Information";
        $valueOptions[2] = "Custom service";
        $valueOptions[3] = "Extras";
        $valueOptions[4] = "My account";
        $elements[7]->setValueOptions($valueOptions);
        if($options!=null and $options->footercolumn)
            $elements[7]->setValue($options->footercolumn);

        $elements[66] = new Element\Submit('submit');
        $elements[66]->setAttribute('id', 'submitbutton');

        if($options!=null)
        {
            $elements[77] = new Element\Hidden('id');
            $elements[77]->setValue($options->id);
        }

        foreach($elements as $e)
        {
            $this->add($e);
        }

        // if there is only one main menu or no menus at all remove the parent input and set menu.parent=0.
        if (sizeof($parents) <= 1)
        {
            $this->remove("parent");
        }
    }
}
