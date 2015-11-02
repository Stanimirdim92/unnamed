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

final class ContentForm extends Form implements InputFilterProviderInterface
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
        parent::__construct("content");
        $this->entityManager = $entityManager;
    }

    public function init()
    {
        $this->setAttribute('method', 'post');
        $this->setAttribute('role', 'form');

        /*
         * Specific image for this content
         */
        $this->add(
            [
            'type' => 'Zend\Form\Element\File',
            'name' => 'preview',
            'options' => [
                'label' => 'Image',
                'object_manager' => $this->entityManager,
                'target_class' => 'Admin\Entity\Content',
                'property' => "preview",
            ],
            'attributes' => [
                'id' => 'preview',
                'class' => 'preview',
            ],
            ]
        );

        /*
         * Gallery for all contents
         */
        $this->add(
            [
            'type' => 'Zend\Form\Element\File',
            'name' => 'imageUpload',
            'attributes' => [
                'id' => 'imgajax',
                'class' => 'imgupload',
                'multiple' => true,
            ],
            'options' => [
                'label' => 'Image',
            ],
            ]
        );

        $this->add(
            [
            'type' => 'Zend\Form\Element\Text',
            'name' => 'title',
            'options' => [
                'label' => 'Title',
                'object_manager' => $this->entityManager,
                'target_class' => 'Admin\Entity\Content',
                'property' => "title",
            ],
            'attributes' => [
                'required'   => "true",
                'size'        => "40",
                'id'         => "seo-caption",
                'placeholder' => 'Title',
            ],
            ]
        );

        $this->add(
            [
            'type' => 'Zend\Form\Element\Text',
            'name' => 'text',
            'options' => [
                'label' => 'Text',
                'object_manager' => $this->entityManager,
                'target_class' => 'Admin\Entity\Content',
                'property' => "text",
            ],
            'attributes' => [
                'class'   => 'ckeditor',
                'rows'        => "5",
                'cols'      => "80",
            ],
            ]
        );

        $valueOptions = [];
        for ($i = 1; $i < 100; $i++) {
            $valueOptions[$i] = $i;
        }
        $this->add([
            'type' => 'DoctrineModule\Form\Element\ObjectSelect',
            'name' => 'menuOrder',
            'options' => [
                'object_manager' => $this->entityManager,
                'disable_inarray_validator' => true,
                'target_class' => 'Admin\Entity\Content',
                'property' => 'menuOrder',
                'display_empty_item' => true,
                'empty_item_label' => 'Please choose menu order (optional)',
                'value_options' => $valueOptions,
                'label' => 'Menu order',
            ],
        ]);

        $this->add(
            [
            'type' => 'DoctrineModule\Form\Element\ObjectSelect',
            'name' => 'type',
            'options' => [
                'label' => 'Type',
                'object_manager' => $this->entityManager,
                'disable_inarray_validator' => true,
                'target_class' => 'Admin\Entity\Content',
                'property' => 'type',
                'display_empty_item' => true,
                'empty_item_label' => 'Please choose your content type',
                'value_options' => [
                    '0' => "Menu",
                    '1' => "News",
                ],
            ],
            ]
        );

        $this->add(
            [
            'type' => 'Zend\Form\Element\Text',
            'name' => 'date',
            'options' => [
                'label' => 'Date',
                'object_manager' => $this->entityManager,
                'target_class' => 'Admin\Entity\Content',
                'property' => "date",
            ],
            'attributes' => [
                'size'  => "20",
            ],
            ]
        );

        $this->add(
            [
            'type' => 'DoctrineModule\Form\Element\ObjectSelect',
            'name' => 'menu',
            'options' => [
                'label' => 'Menu',
                'disable_inarray_validator' => true,
                'object_manager' => $this->entityManager,
                'target_class' => 'Admin\Entity\Menu',
                'property' => "caption",
                'display_empty_item' => true,
                'empty_item_label' => 'Please choose your menu',
                'is_method' => true,
                'find_method' => [
                    'name' => 'getMenus',
                ],
            ],
            ]
        );

        $this->add(
            [
            'type' => 'DoctrineModule\Form\Element\ObjectSelect',
            'name' => 'language',
            'options' => [
                'label' => 'Select language',
                'disable_inarray_validator' => true,
                'object_manager' => $this->entityManager,
                'target_class' => 'Admin\Entity\Language',
                'property' => "name",
                'display_empty_item' => true,
                'empty_item_label' => 'Please choose a language',
                'is_method' => true,
                'find_method' => [
                    'name' => 'getLanguages',
                ],
            ],
            ]
        );

        $this->add(
            [
            'type' => 'Zend\Form\Element\Csrf',
            'name' => 's',
            'options' => [
                'csrf_options' => [
                    'timeout' => 3600,
                ],
            ],
            ]
        );

        $this->add(
            [
            'name' => 'submit',
            'attributes' => [
                'type'  => 'submit',
                'id' => 'submitbutton',
                'value' => "Save",
            ],
            ]
        );

        $this->add(
            [
            'type' => 'Zend\Form\Element\Hidden',
            'name' => 'id',
            'options' => [
                'object_manager' => $this->entityManager,
                'target_class' => 'Admin\Entity\Content',
                'property' => "id",
            ],
            ]
        );

        $this->add(
            [
            'type' => 'Zend\Form\Element\Hidden',
            'name' => 'titleLink',
            'options' => [
                'object_manager' => $this->entityManager,
                'target_class' => 'Admin\Entity\Content',
                'property' => "titleLink",
            ],
            'attributes' => [
                'id' => 'titleLink',
            ],
            ]
        );
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
                "name"=>"title",
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
                "name"=>"text",
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
                "name"=>"menu",
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
                "name"=>"type",
                "required" => false,
                'filters'  => [
                    ['name' => 'Int'],
                ],
                'validators' => [
                    [
                        'name' => 'Regex',
                        'options' => [
                            'pattern' => '/^[0-1]+$/',
                        ],
                    ],
                ],
            ],
            [
                "name"=>"date",
                "required" => false,
                'filters' => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                ],
            ],
            [
                "name"=>"preview",
                "required" => false,
                'validators' => [
                    [
                        'name' => 'Zend\Validator\File\Size',
                        'options' => [
                            'min' => '5kB',
                            'max' => '5MB',
                            'useByteString' => true,
                        ],
                    ],
                    [
                        'name' => 'Zend\Validator\File\Extension',
                        'options' => [
                            'extension' => [
                                'jpg',
                                'gif',
                                'png',
                                'jpeg',
                                'bmp',
                                'webp',
                            ],
                            'case' => true,
                        ],
                    ],
                ],
            ],
            [
                "name"=>"imageUpload",
                "required" => false,
            ],
            [
                "name"=>"titleLink",
                "required" => true,
                'filters' => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                    ['name' => 'StringToLower'],
                ],
            ],
        ];
    }
}
