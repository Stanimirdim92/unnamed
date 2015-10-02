<?php

/**
 * @copyright  2015 (c) Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 *
 * @version    0.0.15
 *
 * @link       TBA
 */

namespace Admin\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;

class ContentForm extends Form implements InputFilterProviderInterface
{
    /**
     * @var array $menus
     */
    private $menus = [];

    /**
     * @var array $languages
     */
    private $languages = [];

    /**
     * @param Zend\Db\ResultSet\ResultSet $languages
     * @param array $menus
     */
    public function __construct($languages, $menus = null)
    {
        $this->languages = $languages;
        $this->menus = $menus;

        parent::__construct("content");
    }

    private function collectLanguageOptions()
    {
        $valueOptions = [];
        foreach ($this->languages as $language) {
            $valueOptions[$language->getId()] = $language->getName();
        }

        return $valueOptions;
    }

    private function collectMenuOptions()
    {
        $valueOptions = [];
        if ($this->menus) {
            foreach ($this->menus as $submenus) {
                $valueOptions[$submenus->getId()] = $submenus->getCaption();
            }
            return $valueOptions;
        }
        return;
    }

    public function init()
    {
        $this->setAttribute('method', 'post');
        $this->setAttribute('role', 'form');

        /*
         * Specific image for this content
         */
        $this->add([
            'type' => 'Zend\Form\Element\File',
            'name' => 'preview',
            'attributes' => [
                'id' => 'preview',
                'class' => 'preview',
            ],
            'options' => [
                'label' => 'Image',
            ],
        ]);

        /*
         * Gallery for all contents
         */
        $this->add([
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
        ]);

        $this->add([
            'type' => 'Zend\Form\Element\Text',
            'name' => 'title',
            'attributes' => [
                'required'   => true,
                'size'        => 40,
                'id'         => "seo-caption",
                'placeholder' => 'Title',
            ],
            'options' => [
                'label' => 'Title',
            ],
        ]);

        $this->add([
            'type' => 'Zend\Form\Element\Text',
            'name' => 'text',
            'attributes' => [
                'class'   => 'ckeditor',
                'rows'        => 5,
                'cols'      => 80,
            ],
            'options' => [
                'label' => 'Text',
            ],
        ]);

        $valueOptions = [];
        for ($i = 1; $i < 100; $i++) {
            $valueOptions[$i] = $i;
        }
        $this->add([
            'type' => 'Zend\Form\Element\Select',
            'name' => 'menuOrder',
            'options' => [
                'empty_option' => 'Please choose menu order (optional)',
                'value_options' => $valueOptions,
                'label' => 'Menu order',
            ],
        ]);

        $this->add([
            'type' => 'Zend\Form\Element\Select',
            'name' => 'type',
            'options' => [
                'label' => 'Type',
                'empty_option' => 'Please choose your content type',
                'value_options' => [
                    '0' => "Menu",
                    '1' => "News",
                ],
            ],
        ]);

        $this->add([
            'type' => 'Zend\Form\Element\Text',
            'name' => 'date',
            'attributes' => [
                'size'  => 20,
            ],
            'options' => [
                'label' => 'Date',
            ],
        ]);

        $this->add([
            'type' => 'Zend\Form\Element\Select',
            'name' => 'menu',
            'options' => [
                'label' => 'Menu',
                'empty_option' => 'Please choose your menu',
                'value_options' => $this->collectMenuOptions(),
            ],
        ]);

        $this->add([
            'type' => 'Zend\Form\Element\Select',
            'name' => 'language',
            'options' => [
                'label' => 'Language',
                'empty_option' => 'Please choose a language',
                'value_options' => $this->collectLanguageOptions(),
            ],
        ]);

        $this->add([
            'type' => 'Zend\Form\Element\Csrf',
            'name' => 's',
            'options' => [
                'csrf_options' => [
                    'timeout' => 3600,
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

        $this->add([
            'type' => 'Zend\Form\Element\Hidden',
            'name' => 'titleLink',
            'attributes' => [
                'id' => 'titleLink',
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
