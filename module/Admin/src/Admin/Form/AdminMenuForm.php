<?php
namespace Admin\Form;
use Zend\Form\Form;
use Zend\Form\Element;
class AdminMenuForm extends Form
{
    public function __construct($options = null, $parents = array())
    {
        parent::__construct("admin-menu");
        $elements = array();

        $elements[0] = new Element\Text('caption');
        $elements[0]->setLabel('Caption');
        $elements[0]->setAttributes(array(
            'required'   => true,
            'size'        => 40,
            'class'      => 'admin-menu-caption',
            'placeholder' => 'Caption',
        ));

        if($options!=null and $options->caption)
            $elements[0]->setValue($options->caption);

        $elements[1] = new Element\Select('menuOrder');
        $elements[1]->setLabel('menuOrder');
        $valueOptions = array();
        for($i = 1; $i<50; $i++)
        {
              $valueOptions[$i] = $i;
        }
        $elements[1]->setValueOptions($valueOptions);
        if($options!=null and $options->menuOrder)
            $elements[1]->setValue($options->menuOrder);
        else
            $elements[1]->setValue(0);

        $elements[2] = new Element\Checkbox('advanced');
        $elements[2]->setLabel('Advanced');
        if($options!=null and $options->advanced)
            $elements[2]->setValue($options->advanced);

        $elements[3] = new Element\Text('controller');
        $elements[3]->setLabel('Controller');
        $elements[3]->setAttributes(array(
            'required'   => false,
            'size'        => 40,
            'class'      => 'admin-menu-controller',
            'placeholder' => 'Controller',
        ));

        if($options!=null and $options->controller)
            $elements[3]->setValue($options->controller);

        $elements[4] = new Element\Text('action');
        $elements[4]->setLabel('Action');
        $elements[4]->setAttributes(array(
            'required'   => false,
            'size'        => 40,
            'class'      => 'admin-menu-action',
            'placeholder' => 'Action',
        ));
        if($options!=null and $options->action)
            $elements[4]->setValue($options->action);

        $elements[5] = new Element\Text('class');
        $elements[5]->setLabel('Class');
        $elements[5]->setAttributes(array(
            'required'   => false,
            'size'        => 40,
            'class'      => 'admin-menu-class',
            'placeholder' => 'Class',
        ));

        if($options!=null and $options->class)
            $elements[5]->setValue($options->class);

        $elements[6] = new Element\Text('description');
        $elements[6]->setLabel('Description');
        $elements[6]->setAttributes(array(
            'required'   => false,
            'size'        => 40,
            'class'      => 'admin-menu-description',
            'placeholder' => 'Description',
        ));

        if($options!=null and $options->description)
            $elements[6]->setValue($options->description);

        $elements[7] = new Element\Select('parent');
        $elements[7]->setLabel('parent');
        $valueOptions = array();

        $valueOptions[0] = 'Parent menu';        
        foreach($parents as $item)
        {
            $valueOptions[$item->id] = $item->toString();
        }
        $elements[7]->setValueOptions($valueOptions);
        if($options!=null and $options->parent)
        {
            $elements[7]->setValue($options->parent);
        }

        $elements[8] = new Element\Submit('submit');
        $elements[8]->setAttribute('id', 'submitbutton');

        if($options!=null)
        {
            $elements[9] = new Element\Hidden('id');
            $elements[9]->setValue($options->id);
        }

        foreach($elements as $e)
        {
            $this->add($e);
        }
    }
}
