<?php
namespace Admin\Form;

use Zend\Form\Form;
use Zend\Form\Element;

class UserForm extends Form
{
    public function __construct($options = null, $languages = array(), $currency = array())
    {
        parent::__construct("user");
        $elements = array();

        $elements[0] = new Element\Text('name');
        $elements[0]->setLabel('Name');
        $elements[0]->setAttributes(array(
            'required'   => false,
            'size'        => 40,
            'class'      => 'user-name',
            'placeholder' => 'Name',
        ));

        if ($options!=null and $options->name) {
            $elements[0]->setValue($options->name);
        }

        $elements[1] = new Element\Text('surname');
        $elements[1]->setLabel('Surname');
        $elements[1]->setAttributes(array(
            'required'   => false,
            'size'        => 40,
            'class'      => 'user-surname',
            'placeholder' => 'Surname',
        ));

        if ($options!=null and $options->surname) {
            $elements[1]->setValue($options->surname);
        }

        $elements[3] = new Element\Text('email');
        $elements[3]->setLabel('Email');
        $elements[3]->setAttributes(array(
            'required'   => true,
            'size'        => 40,
            'class'      => 'user-email',
            'placeholder' => 'Email',
        ));

        if ($options!=null and $options->email) {
            $elements[3]->setValue($options->email);
        }

        $elements[4] = new Element\Text('birthDate');
        $elements[4]->setLabel('Birthday');
        $elements[4]->setAttributes(array(
            'required'   => false,
            'size'        => 40,
            'class'      => 'datetimepicker',
            'placeholder' => 'YYYY-MM-DD',
        ));
        if ($options!=null and $options->birthDate) {
            $elements[4]->setValue($options->birthDate);
        } else {
            $elements[4]->setValue("0000-00-00");
        }

        // $elements[6] = new Element\Checkbox('admin');
        // $elements[6]->setLabel('Admin');
        // if($options!=null and $options->admin)
        //     $elements[6]->setValue($options->admin);

        $elements[7] = new Element\Checkbox('deleted');
        $elements[7]->setLabel('Disabled');
        if ($options!=null and $options->deleted) {
            $elements[7]->setValue($options->deleted);
        }

        $elements[8] = new Element\Select('language');
        $elements[8]->setLabel('Language');
        $valueOptions = array();
       
        foreach ($languages as $item) {
            $valueOptions[$item->id] = $item->toString();
        }
        $elements[8]->setValueOptions($valueOptions);
        if ($options!=null and $options->language) {
            $elements[8]->setValue($options->language);
        }

        $elements[9] = new Element\Select('currency');
        $elements[9]->setLabel('Currency');
        $valueOptions = array();
        foreach ($currency as $item) {
            $valueOptions[$item->id] = $item->toString();
        }
        $elements[9]->setValueOptions($valueOptions);
        if ($options!=null and $options->currency) {
            $elements[9]->setValue($options->currency);
        }

        $elements[11] = new Element\Submit('submit');
        $elements[11]->setAttribute('id', 'submitbutton');

        if ($options!=null) {
            $elements[12] = new Element\Hidden('id');
            $elements[12]->setValue($options->id);
        }
        
        foreach ($elements as $e) {
            $this->add($e);
        }
        $this->remove("language");
        $this->remove("currency");
    }
}
