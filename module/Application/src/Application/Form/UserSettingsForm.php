<?php
namespace Application\Form;

use Zend\Form\Form;
use Zend\Form\Element;

class UserSettingsForm extends Form
{
    public function __construct($options = null)
    {
        parent::__construct("usersettings");
        $elements = array();

        $elements[0] = new Element\Text('name');
        $elements[0]->setAttributes(array(
            'required'   => false,
            'size'        => 40,
        ));

        if($options!=null and $options->name)
            $elements[0]->setValue($options->name);

        $elements[1] = new Element\Text('surname');
        $elements[1]->setAttributes(array(
            'required'   => false,
            'size'        => 40,
        ));

        if($options!=null and $options->surname)
            $elements[1]->setValue($options->surname);

        $elements[2] = new Element\Password("password");
        $elements[2]->setAttributes(array(
            // 'required'    => true,
            'size'        => 40,
            'placeholder' => '123456789',
        ));

        $elements[4] = new Element\Text('email');
        $elements[4]->setAttributes(array(
            'required'   => true,
            'size'        => 40,
            'placeholder' => 'johnsmith@example.com',
        ));

        if($options!=null and $options->email)
            $elements[4]->setValue($options->email);

        $elements[5] = new Element\Text('birthDate');
        $elements[5]->setAttributes(array(
            'required'   => false,
            'size'        => 40,
            'placeholder' => 'YYYY-MM-DD',
        ));
        if($options!=null and $options->birthDate)
            $elements[5]->setValue($options->birthDate);
        else
            $elements[5]->setValue("YYYY-MM-DD");

        $elements[8] = new Element\Csrf('s');
        $elements[11] = new Element\Submit('submit');
        $elements[11]->setAttributea(array(
            'id' => 'submitbutton'
        ));

        if($options!=null)
        {
            $elements[12] = new Element\Hidden('id');
            $elements[12]->setValue($options->id);
        }

        foreach($elements as $e)
        {
            $this->add($e);
        }
    }
}