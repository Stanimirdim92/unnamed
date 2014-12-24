<?php
namespace Admin\Form;

use Zend\Form\Form;
use Zend\Form\Element;

class ContentForm extends Form
{
    public function __construct($options = null, $menus = array(), $languages = array())
    {
        parent::__construct("content");
        $elements = array();

        $elements[0] = new Element\Text('title');
        $elements[0]->setLabel('Title');
        $elements[0]->setAttributes(array(
            'required'   => true,
            'size'        => 40,
            'placeholder' => 'Title',
        ));

        if($options!=null and $options->title)
            $elements[0]->setValue($options->title);

        if($options->preview!=null)
        {
            $elements[101] = new Element\Image('currentpreview');
            $elements[101]->setLabel('Current preview')
                          ->setAttribute('id', 'currentpreview')
                          ->setAttribute('height', 100)
                          ->setAttribute('disabled', 'disabled')
                          ->setAttribute('class', 'file')
                          ->setAttribute('src', '/userfiles/preview/'.$options->preview);
            $elements[201] = new Element\Checkbox('removepreview');
        	$elements[201]->setLabel('Remove preview')
                          	      ->setAttribute('id', 'removepreview');
            // if($options!=null and $options->preview)
            //     $elements[1]->setValue($options->preview);
        }
        else
        {
            $elements[1] = new Element\File('preview');
            $elements[1]->setLabel('Preview')
                          ->setAttribute('id', 'preview')
                          ->setAttribute('class', 'file')->setValue('/userfiles/preview/default_logo.png');
        }

        $elements[2] = new Element\Textarea('extract');
        $elements[2]->setLabel('Extract')
                          ->setAttribute('class', 'tinymce')
                          ->setAttribute('rows', 15)
                          ->setAttribute('cols', 80);
        if($options!=null and $options->extract)
            $elements[2]->setValue($options->extract);

        $elements[3] = new Element\Textarea('text');
        $elements[3]->setLabel('Text')
                          ->setAttribute('class', 'tinymce')
                          ->setAttribute('rows', 15)
                          ->setAttribute('cols', 80);
        if($options!=null and $options->text)
            $elements[3]->setValue($options->text);

        $elements[4] = new Element\Select('menuOrder');
        $elements[4]->setLabel('Menu order');
        $valueOptions = array();
        for($i = 1; $i<40; $i++)
        {
              $valueOptions[$i] = $i;
        }
        $elements[4]->setValueOptions($valueOptions);
        if($options!=null and $options->menuOrder)
            $elements[4]->setValue($options->menuOrder);
        else
            $elements[4]->setValue(0);

        $elements[5] = new Element\Select('type');
        $elements[5]->setLabel('type');
        $valueOptions = array();
        $valueOptions[0] = "menu";
        $valueOptions[1] = "news";
        $elements[5]->setValueOptions($valueOptions);
        if($options!=null and $options->type)
            $elements[5]->setValue($options->type);

        $elements[6] = new Element\Text('date');
        $elements[6]->setLabel('Date')
                        ->setAttribute('size', 20);
        if($options!=null and $options->date)
            $elements[6]->setValue($options->date);
        else
            $elements[6]->setValue(date("Y-m-d H:i:s"));

        $elements[7] = new Element\Select('menu');
        $elements[7]->setLabel('menu');
        $valueOptions = array();
        $valueOptions[0] = 'Select a menu';        

        foreach ($menus as $menu)
        {
            if ($menu->parent != 0)
            {
               $valueOptions[$menu->id] = "--".$menu->toString();
            }
            else
            {
                $valueOptions[$menu->id] = $menu->toString();
            }
        }
        $elements[7]->setValueOptions($valueOptions);

        if($options!=null and $options->menu)
        {
            $elements[7]->setValue($options->menu);
        }

        $elements[8] = new Element\Select('language');
        $elements[8]->setLabel('language');
        $valueOptions = array();
        
        foreach($languages as $item)
        {
            $valueOptions[$item->id] = $item->toString();
        }
        $elements[8]->setValueOptions($valueOptions);
        if($options!=null and $options->language)
        {
            $elements[8]->setValue($options->language);
        }

        $elements[9] = new Element\Submit('submit');
        $elements[9]->setAttribute('id', 'submitbutton');

        if($options!=null)
        {
            $elements[10] = new Element\Hidden('id');
            $elements[10]->setValue($options->id);
        }

        foreach($elements as $e)
        {
            $this->add($e);
        }

        // if there are no menus with the current session language, simply remove the menu.
        if (sizeof($menus) <= 0)
        {
            $this->remove("menu");
        }
    }
}
