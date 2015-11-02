<?php

/**
 * @copyright  2015 (c) Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 *
 * @version    0.0.21
 *
 * @link       TBA
 */

namespace Admin\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;
use Doctrine\ORM\EntityManager;

final class AdminMenuForm extends Form implements InputFilterProviderInterface
{
    /**
     * @var Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     * @param $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        parent::__construct("admin-menu");
        $this->entityManager = $entityManager;
    }

    public function init()
    {
        $this->setAttribute('method', 'post');
        $this->setAttribute('role', 'form');

        $this->add([
            'type' => 'Zend\Form\Element\Text',
            'name' => 'caption',
            'options' => [
                'label' => 'Caption',
                'object_manager' => $this->entityManager,
                'target_class' => 'Admin\Entity\AdminMenu',
                'property' => "caption",
            ],
            'attributes' => [
                'required'   => "true",
                'size'        => "40",
                'class'      => 'admin-menu-caption',
                'placeholder' => 'Caption',
            ],
        ]);

        $valueOptions = [];
        for ($i = 1; $i < 150; $i++) {
            $valueOptions[$i] = $i;
        }
        $this->add([
            'type' => 'DoctrineModule\Form\Element\ObjectSelect',
            'name' => 'menuOrder',
            'options' => [
                'object_manager' => $this->entityManager,
                'disable_inarray_validator' => true,
                'target_class' => 'Admin\Entity\AdminMenu',
                'property' => 'menuOrder',
                'display_empty_item' => true,
                'empty_item_label' => 'Please choose menu order (optional)',
                'value_options' => $valueOptions,
                'label' => 'Menu order',
            ],
        ]);

        $this->add([
            'type' => 'Zend\Form\Element\Text',
            'name' => 'controller',
            'options' => [
                'label' => 'Controller',
                'object_manager' => $this->entityManager,
                'target_class' => 'Admin\Entity\AdminMenu',
                'property' => "controller",
            ],
            'attributes' => [
                'size'        => "40",
                'class'       => 'admin-menu-controller',
                'placeholder' => 'Controller',
            ],
        ]);

        $this->add([
            'type' => 'Zend\Form\Element\Text',
            'name' => 'class',
            'options' => [
                'label' => 'CSS class',
                'object_manager' => $this->entityManager,
                'target_class' => 'Admin\Entity\AdminMenu',
                'property' => "class",
            ],
            'attributes' => [
                'size'        => "40",
                'class'       => 'admin-menu-class',
                'placeholder' => 'CSS class',
            ],
        ]);

        $this->add([
            'type' => 'Zend\Form\Element\Text',
            'name' => 'action',
            'options' => [
                'label' => 'Action',
                'object_manager' => $this->entityManager,
                'target_class' => 'Admin\Entity\AdminMenu',
                'property' => "action",
            ],
            'attributes' => [
                'size'        => "40",
                'class'       => 'admin-menu-action',
                'placeholder' => 'Action',
            ],
        ]);

        $this->add([
            'type' => 'Zend\Form\Element\Text',
            'name' => 'description',
            'options' => [
                'label' => 'Description',
                'object_manager' => $this->entityManager,
                'target_class' => 'Admin\Entity\AdminMenu',
                'property' => "caption",
            ],
            'attributes' => [
                'size'        => "40",
                'class'       => 'admin-menu-description',
                'placeholder' => 'Description',
            ],
        ]);

        $this->add([
            'type' => 'DoctrineModule\Form\Element\ObjectSelect',
            'name' => 'parent',
            'options' => [
                'label' => 'Parent admin menu',
                'disable_inarray_validator' => true,
                'object_manager' => $this->entityManager,
                'target_class' => 'Admin\Entity\AdminMenu',
                'property' => "caption",
                'display_empty_item' => true,
                'empty_item_label' => "Select parent admin menu",
                'is_method' => true,
                'find_method' => [
                    'name' => 'getParentMenus',
                ],
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
                'value' => "Save",
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
                'name' => 'id',
                'required' => false,
                'filters' => [
                    ['name' => 'Int'],
                ],
            ],
            [
                "name"=>"caption",
                "required" => true,
                'filters' => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                ],
                'validators' => [
                    ['name' => 'NotEmpty'],
                    [
                        'name'    => 'StringLength',
                        'options' => [
                            'encoding' => 'UTF-8',
                            'min' => 1,
                            'max' => 200,
                        ],
                    ],
                ],
            ],
            [
                "name"=>"menuOrder",
                "required" => false,
                'filters'  => [
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
            [
                "name"=>"controller",
                "required" => false,
                'filters' => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                ],
                'validators' => [
                    ['name' => 'NotEmpty'],
                    [
                        'name'    => 'StringLength',
                        'options' => [
                            'encoding' => 'UTF-8',
                            'min' => 1,
                            'max' => 200,
                        ],
                    ],
                ],
            ],
            [
                "name"=>"action",
                "required" => false,
                'filters' => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                ],
                'validators' => [
                    ['name' => 'NotEmpty'],
                    [
                        'name'    => 'StringLength',
                        'options' => [
                            'encoding' => 'UTF-8',
                            'min' => 1,
                            'max' => 200,
                        ],
                    ],
                ],
            ],
            [
                "name"=>"class",
                "required" => false,
                'filters' => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                ],
                'validators' => [
                    ['name' => 'NotEmpty'],
                    [
                        'name'    => 'StringLength',
                        'options' => [
                            'encoding' => 'UTF-8',
                            'min' => 1,
                            'max' => 50,
                        ],
                    ],
                ],
            ],
            [
                "name"=>"description",
                "required" => false,
                'filters' => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                ],
                'validators' => [
                    [
                        'name'    => 'StringLength',
                        'options' => [
                            'encoding' => 'UTF-8',
                            'min' => 0,
                            'max' => 150,
                        ],
                    ],
                ],
            ],
            [
                "name"=>"parent",
                "required" => false,
                'filters'  => [
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
