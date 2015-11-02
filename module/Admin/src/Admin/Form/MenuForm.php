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

final class MenuForm extends Form implements InputFilterProviderInterface
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
        parent::__construct("menu");
        $this->entityManager = $entityManager;
    }

    public function init()
    {
        $this->setAttribute('method', 'post');
        $this->setAttribute('role', 'form');

        $this->add([
            'type' => 'Zend\Form\Element\Text',
            'name' => 'caption',
            'attributes' => [
                'required' => "true",
                'size' => "40",
                'id' => "seo-caption",
                'placeholder' => 'Caption',
            ],
            'options' => [
                'label' => 'Caption',
                'object_manager' => $this->entityManager,
                'target_class' => 'Admin\Entity\Menu',
                'property' => "caption",
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
                'target_class' => 'Admin\Entity\Menu',
                'property' => 'menuOrder',
                'display_empty_item' => true,
                'empty_item_label' => 'Please choose menu order (optional)',
                'value_options' => $valueOptions,
                'label' => 'Menu order',
            ],
        ]);

        $this->add([
            'type' => 'Zend\Form\Element\Text',
            'name' => 'keywords',
            'options' => [
                'object_manager' => $this->entityManager,
                'target_class' => 'Admin\Entity\Menu',
                'property' => "keywords",
                'label' => 'Keywords',
            ],
            'attributes' => [
                'size'        => "40",
                'placeholder' => 'Keywords (max 15 words) seperated by commas',
            ],
        ]);

        $this->add([
            'type' => 'Zend\Form\Element\Text',
            'name' => 'description',
            'options' => [
                'object_manager' => $this->entityManager,
                'target_class' => 'Admin\Entity\Menu',
                'property' => "description",
                'label' => 'Description',
            ],
            'attributes' => [
                'size'        => "40",
                'placeholder' => 'Description (max 150 characters)',
            ],
        ]);

        $this->add([
            'type' => 'Zend\Form\Element\Text',
            'name' => 'language',
            'options' => [
                'label' => 'Language',
                'object_manager' => $this->entityManager,
                'target_class' => 'Admin\Entity\Menu',
                'property' => "language",
            ],
            'attributes' => [
                'size'        => "40",
                'placeholder' => 'Language',
            ],
        ]);

        $this->add([
            'type' => 'DoctrineModule\Form\Element\ObjectSelect',
            'name' => 'parent',
            'options' => [
                'label' => 'Parent menu',
                'disable_inarray_validator' => true,
                'object_manager' => $this->entityManager,
                'target_class' => 'Admin\Entity\Menu',
                'property' => "caption",
                'display_empty_item' => true,
                'empty_item_label' => 'Please choose parent menu',
                'is_method' => true,
                'find_method' => [
                    'name' => 'getMenus',
                ],
            ],
        ]);

        $valueOptions = [];
        $valueOptions[0] = "Main menu";
        $valueOptions[1] = "Left menu";
        $valueOptions[2] = "Right menu";
        $valueOptions[3] = "Footer menu";
        $this->add([
            'type' => 'DoctrineModule\Form\Element\ObjectSelect',
            'name' => 'menutype',
            'options' => [
                'object_manager' => $this->entityManager,
                'disable_inarray_validator' => true,
                'target_class' => 'Admin\Entity\Menu',
                'property' => "menutype",
                'display_empty_item' => true,
                'empty_item_label' => 'Please choose menu type',
                'value_options' => $valueOptions,
                'label' => 'Choose menu type',
            ],
        ]);

        $this->add([
            'type' => 'Zend\Form\Element\Text',
            'name' => 'class',
            'options' => [
                'object_manager' => $this->entityManager,
                'target_class' => 'Admin\Entity\Menu',
                'property' => "class",
                'label' => 'CSS class',
            ],
            'attributes' => [
                'size'        => "40",
                'class'       => 'admin-menu-class',
                'placeholder' => 'CSS class',
            ],
        ]);

        $valueOptions = [];
        // 0 index missed intentionally
        $valueOptions[1] = "Column one";
        $valueOptions[2] = "Column two";
        $valueOptions[3] = "Column three";
        $valueOptions[4] = "Column four";
        $this->add([
            'type' => 'DoctrineModule\Form\Element\ObjectSelect',
            'name' => 'footercolumn',
            'options' => [
                'object_manager' => $this->entityManager,
                'target_class' => 'Admin\Entity\Menu',
                'property' => "footercolumn",
                'display_empty_item' => true,
                'empty_item_label' => 'Please choose footer column',
                'value_options' => $valueOptions,
                'label' => 'Choose footer column',
            ],
        ]);

        $this->add(
            [
            'type' => 'Zend\Form\Element\Csrf',
            'name' => 's',
            'options' => [
                'csrf_options' => [
                    'timeout' => 1400,
                ],
            ],
            ]
        );

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
            'options' => [
                'object_manager' => $this->entityManager,
                'target_class' => 'Admin\Entity\Menu',
                'property' => "id",
            ],
        ]);

        $this->add([
            'type' => 'Zend\Form\Element\Hidden',
            'name' => 'menulink',
            'attributes' => [
                'id' => 'menulink',
            ],
            'options' => [
                'object_manager' => $this->entityManager,
                'target_class' => 'Admin\Entity\Menu',
                'property' => "menulink",
            ],
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
                "name"=>"language",
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
            [
                "name"=>"keywords",
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
                            'max' => 300,
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
                "name"=>"menutype",
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
                "name"=>"footercolumn",
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
                "name"=>"menulink",
                "required" => false,
                'filters' => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                    ['name' => 'StringToLower'],
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
        ];
    }
}
