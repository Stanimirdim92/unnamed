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

class MenuForm extends Form implements InputFilterProviderInterface
{
    /**
     * @var Zend\Db\ResultSet\ResultSet $menus
     */
    private $menus = null;

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

        parent::__construct("menu");
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
        return null;
    }

    public function init()
    {
        $this->setAttribute('method', 'post');
        $this->setAttribute('role', 'form');

        $this->add([
            'type' => 'Zend\Form\Element\Text',
            'name' => 'caption',
            'attributes' => [
                'required'   => true,
                'size'        => 40,
                'id'         => "seo-caption",
                'placeholder' => 'Caption',
            ],
            'options' => [
                'label' => 'Caption',
            ],
        ]);

        $valueOptions = [];
        for ($i = 1; $i < 150; $i++) {
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
            'type' => 'Zend\Form\Element\Text',
            'name' => 'keywords',
            'attributes' => [
                'required'   => false,
                'size'        => 40,
                'placeholder' => 'Keywords (max 15 words) seperated by commas',
            ],
            'options' => [
                'label' => 'Keywords',
            ],
        ]);

        $this->add([
            'type' => 'Zend\Form\Element\Text',
            'name' => 'description',
            'attributes' => [
                'required'   => false,
                'size'        => 40,
                'placeholder' => 'Description (max 150 characters)',
            ],
            'options' => [
                'label' => 'Description',
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
            'type' => 'Zend\Form\Element\Select',
            'name' => 'parent',
            'options' => [
                'label' => 'Parent menu',
                'disable_inarray_validator' => true,
                'empty_option' => 'Please choose your menu',
                'value_options' => $this->collectMenuOptions(),
            ],
        ]);

        $valueOptions = [];
        $valueOptions[0] = "Main menu";
        $valueOptions[1] = "Left menu";
        $valueOptions[2] = "Right menu";
        $valueOptions[3] = "Footer menu";
        $this->add([
            'type' => 'Zend\Form\Element\Select',
            'name' => 'menutype',
            'options' => [
                'empty_option' => 'Please choose menu type',
                'value_options' => $valueOptions,
                'label' => 'Choose menu type',
            ],
        ]);

        $this->add([
            'type' => 'Zend\Form\Element\Text',
            'name' => 'class',
            'attributes' => [
                'required'   => false,
                'size'        => 40,
                'class'       => 'admin-menu-class',
                'placeholder' => 'CSS class',
            ],
            'options' => [
                'label' => 'CSS class',
            ],
        ]);

        $valueOptions = [];
        // 0 index missed intentionally
        $valueOptions[1] = "Column one";
        $valueOptions[2] = "Column two";
        $valueOptions[3] = "Column three";
        $valueOptions[4] = "Column four";
        $this->add([
            'type' => 'Zend\Form\Element\Select',
            'name' => 'footercolumn',
            'options' => [
                'empty_option' => 'Please choose footer column',
                'value_options' => $valueOptions,
                'label' => 'Choose footer column',
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

        $this->add([
            'type' => 'Zend\Form\Element\Hidden',
            'name' => 'menulink',
            'attributes' => [
                'id' => 'menulink',
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
                            'max' => 200,
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
