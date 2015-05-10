<?php
namespace Admin\Form;

use Zend\Form\Form;
use Zend\Form\Element;

class PurchaseForm extends Form
{
    public function __construct($options = null, $currency = [])
    {
        parent::__construct('purchase');

        $elements = [];

        $elements[0] = new Element\Text('user');
        $elements[0]->setLabel('User ID');
        $elements[0]->setAttributes([
            'required'   =>  true,
            'size'        => 40,
            'placeholder' => 'User ID',
        ]);
        if ($options!=null and $options->user) {
            $elements[0]->setValue($options->user);
        }

        $elements[1] = new Element\Text('purchasedate');
        $elements[1]->setLabel('Purchase date');
        $elements[1]->setAttributes([
            'required'   =>  true,
            'size'        => 40,
            'placeholder' => 'Purchase date - YYYY-MM-DD',
        ]);
        if ($options!=null and $options->purchasedate) {
            $elements[1]->setValue($options->purchasedate);
        } else {
            $elements[1]->setValue(date("Y-m-d H:i:s"));
        }

        $elements[2] = new Element\Text('packet');
        $elements[2]->setLabel('Packet ID');
        $elements[2]->setAttributes([
            'required'   =>  true,
            'size'        => 40,
            'placeholder' => 'Packet ID',
        ]);
        if ($options!=null and $options->packet) {
            $elements[2]->setValue($options->packet);
        }

        $elements[3] = new Element\Text('packetexpires');
        $elements[3]->setLabel('Purchase months');
        $elements[3]->setAttributes([
            'required'   =>  true,
            'size'        => 40,
            'placeholder' => 'For how many months the packet will be active',
        ]);
        if ($options!=null and $options->packetexpires) {
            $elements[3]->setValue($options->packetexpires);
        }

        $elements[4] = new Element\Checkbox('payed');
        $elements[4]->setLabel('Is packet payed');
        if ($options!=null and $options->payed) {
            $elements[4]->setValue($options->payed);
        }

        $elements[5] = new Element\Checkbox('active');
        $elements[5]->setLabel('Is packet active');

        if ($options!=null and $options->active) {
            $elements[5]->setValue($options->active);
        }

        $elements[6] = new Element\Text('money');
        $elements[6]->setAttributes([
            'required'   =>  true,
            'size'        => 40,
            'placeholder' => 'Total price of the packet',
        ]);
        $elements[6]->setLabel('Packet price');

        if ($options!=null and $options->money) {
            $elements[6]->setValue($options->money);
        }

        $elements[7] = new Element\Select('currency');
        $elements[7]->setLabel('Currency');
        $valueOptions = [];
        foreach ($currency as $item) {
            $valueOptions[$item->id] = $item->toString();
        }
        $elements[7]->setValueOptions($valueOptions);
        if ($options!=null and $options->currency) {
            $elements[7]->setValue($options->currency);
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
    }
}
