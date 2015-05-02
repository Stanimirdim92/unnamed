<?php
namespace Admin\Form;

use Zend\Form\Form;
use Zend\Form\Element;

class PacketForm extends Form
{
    public function __construct($options = null)
    {
        parent::__construct("packet");
        $elements = array();

        $elements[0] = new Element\Text('diskspace');
        $elements[0]->setLabel('Disk space');
        $elements[0]->setAttributes(array(
            'required'   => true,
            'size'        => 40,
            'placeholder' => 'Disk space',
        ));

        if ($options!=null && $options->diskspace) {
            $elements[0]->setValue($options->diskspace);
        }

        $elements[1] = new Element\Text('bandwidth');
        $elements[1]->setLabel('Bandwidth');
        $elements[1]->setAttributes(array(
            'required'   => true,
            'size'        => 40,
            'placeholder' => 'B&&width',
        ));

        if ($options!=null && $options->bandwidth) {
            $elements[1]->setValue($options->bandwidth);
        }

        $elements[2] = new Element\Text('domains');
        $elements[2]->setLabel('Domains');
        $elements[2]->setAttributes(array(
            'required'   => true,
            'size'        => 40,
            'placeholder' => 'Domains',
        ));

        if ($options!=null && $options->domains) {
            $elements[2]->setValue($options->domains);
        }

        $elements[3] = new Element\Select('dedictip');
        $elements[3]->setLabel('Dedicated IP addresses');
        $valueOptions = array();
        for ($i = 1; $i<40; $i++) {
            $valueOptions[$i] = $i;
        }
        $elements[3]->setValueOptions($valueOptions);
        if ($options!=null && $options->dedictip) {
            $elements[3]->setValue($options->dedictip);
        } else {
            $elements[3]->setValue(0);
        }

        $elements[5] = new Element\Select('type');
        $elements[5]->setLabel('type');
        $valueOptions = array();
        $valueOptions[0] = "Basic packet";
        $valueOptions[1] = "Normal packet";
        $valueOptions[2] = "Optima packet";
        $valueOptions[3] = "Expert packet";
        $elements[5]->setValueOptions($valueOptions);
        if ($options!=null && $options->type) {
            $elements[5]->setValue($options->type);
        }

        $elements[6] = new Element\Text('domainreg');
        $elements[6]->setLabel('Domain registration');
        $elements[6]->setAttributes(array(
            'required'   => true,
            'size'        => 40,
            'placeholder' => 'Domain registration',
        ));
        if ($options!=null && $options->domainreg) {
            $elements[6]->setValue($options->domainreg);
        }

        $elements[7] = new Element\Text('support');
        $elements[7]->setLabel('Support');
        $elements[7]->setAttributes(array(
            'required'   => true,
            'size'        => 40,
            'placeholder' => 'Support',
        ));
        if ($options!=null && $options->support) {
            $elements[7]->setValue($options->support);
        }

        $elements[8] = new Element\Text('webeditor');
        $elements[8]->setLabel('Web editor');
        $elements[8]->setAttributes(array(
            'required'   => true,
            'size'        => 40,
            'placeholder' => 'Web editor',
        ));
        if ($options!=null && $options->webeditor) {
            $elements[8]->setValue($options->webeditor);
        }

        $elements[9] = new Element\Text('price');
        $elements[9]->setLabel('Price');
        $elements[9]->setAttributes(array(
            'required'   => true,
            'size'        => 40,
            'placeholder' => 'Price',
        ));
        if ($options!=null && $options->price) {
            $elements[9]->setValue($options->price);
        }

        $elements[10] = new Element\Textarea('text');
        $elements[10]->setLabel('Text')
                          ->setAttribute('class', 'ckeditor')
                          ->setAttribute('rows', 15)
                        ->setAttribute('cols', 80);
        if ($options!=null && $options->text) {
            $elements[10]->setValue($options->text);
        }

        $elements[11] = new Element\Text('discount');
        $elements[11]->setLabel('Discount');
        $elements[11]->setAttributes(array(
            'size'        => 40,
            'placeholder' => 'Discount',
        ));
        if ($options!=null && $options->discount != null) {
            $elements[11]->setValue($options->discount);
        }

        $elements[12] = new Element\Text('dollar');
        $elements[12]->setLabel('Dollars course');
        $elements[12]->setAttributes(array(
            'size'        => 40,
            'placeholder' => 'Dollars course',
        ));
        if ($options!=null && $options->dollar != null) {
            $elements[12]->setValue($options->dollar);
        }

        $elements[13] = new Element\Text('euro');
        $elements[13]->setLabel('Euro course');
        $elements[13]->setAttributes(array(
            'size'        => 40,
            'placeholder' => 'Euro course',
        ));
        if ($options!=null && $options->euro != null) {
            $elements[13]->setValue($options->euro);
        }

        $elements[99] = new Element\Submit('submit');
        $elements[99]->setAttribute('id', 'submitbutton');

        if ($options!=null) {
            $elements[110] = new Element\Hidden('id');
            $elements[110]->setValue($options->id);
        }

        foreach ($elements as $e) {
            $this->add($e);
        }
        // $this->remove('type');
    }
}
