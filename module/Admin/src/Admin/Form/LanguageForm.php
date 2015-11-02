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

final class LanguageForm extends Form implements InputFilterProviderInterface
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
        parent::__construct("language");
        $this->entityManager = $entityManager;
    }

    public function init()
    {
        $this->setAttribute('method', 'post');
        $this->setAttribute('role', 'form');

        $this->add(
            [
            'type' => 'Zend\Form\Element\Text',
            'name' => 'name',
            'options' => [
                'label' => 'Name',
                'object_manager' => $this->entityManager,
                'target_class' => 'Admin\Entity\Language',
                'property' => "name",
            ],
            'attributes' => [
                'required'   => "true",
                'size'        => "40",
                'placeholder' => 'Name',
            ],
            ]
        );

        $this->add(
            [
            'type' => 'Zend\Form\Element\Checkbox',
            'name' => 'active',
            'options' => [
                'label' => 'Active',
                'object_manager' => $this->entityManager,
                'target_class' => 'Admin\Entity\Language',
                'property' => "active",
            ],
            ]
        );

        $this->add(
            [
            'type' => 'Zend\Form\Element\Csrf',
            'name' => 's',
            'options' => [
                'csrf_options' => [
                    'timeout' => 500,
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
                'value' => 'Save',
            ],
            ]
        );

        $this->add(
            [
            'type' => 'Zend\Form\Element\Hidden',
            'name' => 'id',
            'options' => [
                'object_manager' => $this->entityManager,
                'target_class' => 'Admin\Entity\Language',
                'property' => "id",
            ],
            ]
        );
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
                "name"=>"name",
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
                            'max' => 10,
                        ],
                    ],
                ],
            ],
            [
                "name"=>"active",
                "required" => false,
                'filters' => [
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
        ];
    }
}
