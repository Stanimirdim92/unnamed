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
            'id'         => "seo-caption",
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
            'placeholder' => 'Keywords (max 15 words) seperate by commas',
        ));
        if($options!=null and $options->keywords)
            $elements[2]->setValue($options->keywords);

        $elements[3] = new Element\Text('description');
        $elements[3]->setLabel('Description');
        $elements[3]->setAttributes(array(
            'required'   => false,
            'size'        => 40,
            'placeholder' => 'Description (max 150 characters)',
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

        $valueOptions[0] = 'Select parent menu';        
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
        $elements[6]->setLabel('Choose menu type');
        $valueOptions = array();
        $valueOptions[0] = "Main menu";
        $valueOptions[1] = "Left menu";
        $valueOptions[2] = "Right menu";
        $valueOptions[3] = "Footer menu";
        $elements[6]->setValueOptions($valueOptions)->setAttribute("id", "menutype");
        if($options!=null and $options->menutype)
            $elements[6]->setValue($options->menutype);

        $elements[7] = new Element\Select('footercolumn');
        $elements[7]->setLabel('Choose footer column');
        $valueOptions = array();
        $valueOptions[1] = "Column one";
        $valueOptions[2] = "Column two";
        $valueOptions[3] = "Column three";
        $valueOptions[4] = "Column four";
        $elements[7]->setValueOptions($valueOptions);
        if($options!=null and $options->footercolumn)
            $elements[7]->setValue($options->footercolumn);

        $elements[66] = new Element\Submit('submit');
        $elements[66]->setAttribute('id', 'submitbutton');

        $elements[8] = new Element\Csrf('s');

        if($options!=null)
        {
            $elements[77] = new Element\Hidden('id');
            $elements[77]->setValue($options->id);
        }
        $elements[78] = new Element\Hidden('menulink');
        $elements[78]->setAttribute('id', 'menulink');
        if($options!=null)
            $elements[78]->setValue($options->menulink);

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
