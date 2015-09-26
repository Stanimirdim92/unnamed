<?php

/**
 * @copyright  2015 (c) Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 *
 * @version    0.0.14
 *
 * @link       TBA
 */

namespace Admin\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;

class AdministratorForm extends Form implements InputFilterProviderInterface
{
    public function __construct()
    {
        parent::__construct("administrator");
    }

    public function init()
    {
        $this->setAttribute('method', 'post');

        $this->add([
            'type' => 'Zend\Form\Element\Text',
            'name' => 'user',
            'attributes' => [
                'required'   => true,
                'size'        => 40,
            'class'      => 'administrator-user ajax-search',
            'placeholder' => 'User ID',
            'autocomplete' => "off",
            ],
            'options' => [
                'label' => 'Caption',
            ],
        ]);

        $this->add([
            'type' => 'Zend\Form\Element\Csrf',
            'name' => 's',
            'options' => [
                'csrf_options' => [
                    'timeout' => 1400,
                ],
            ],
        ]);

        $this->add([
            'name' => 'submit',
            'attributes' => [
                'type'  => 'submit',
                'id' => 'submitbutton',
            ],
        ]);

        $this->add([
            'type' => 'Zend\Form\Element\Hidden',
            'name' => 'id',
        ]);
    }

    public function getInputFilterSpecification()
    {
        return [
            [
                'name'     => 'id',
                'required' => false,
                'filters'  => [
                    ['name' => 'Int'],
                ],
            ],
            [
                "name"=>"user",
                "required" => true,
                'filters' => [
                    ['name' => 'Int'],
                ],
                'validators' => [
                    [
                        'name' => 'Regex',
                        'options' => [
                            'pattern' => '/^[0-9]+$/',
                        ],
                    ],
                ],
            ],
        ];
    }
}
