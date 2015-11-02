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

final class SettingsPostsForm extends Form implements InputFilterProviderInterface
{
    /**
     * @var array
     */
    private $config = [];

    /**
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->config = $config;

        parent::__construct("settings-posts");
    }

    public function init()
    {
        $this->setAttribute('method', 'post');
        $this->setAttribute('role', 'form');

        $this->add(
            [
            'type' => 'Zend\Form\Element\Number',
            'name' => 'content',
            'attributes' => [
                'required'   => true,
                'min'        => 1,
                'step'      => 1,
                'size'        => 40,
                'class'      => 'settings_content',
                'placeholder' => 'Content posts per page',
                'value' => $this->config['content'],
            ],
            'options' => [
                'label' => 'Content posts per page',
            ],
            ]
        );

        $this->add(
            [
            'type' => 'Zend\Form\Element\Number',
            'name' => 'menu',
            'attributes' => [
                'required'   => true,
                'min'        => 1,
                'step'      => 1,
                'size'        => 40,
                'class'       => 'settings_menu',
                'placeholder' => 'Menu posts per page',
                'value' => $this->config['menu'],
            ],
            'options' => [
                'label' => 'Menu posts per page',
            ],
            ]
        );

        $this->add(
            [
            'type' => 'Zend\Form\Element\Number',
            'name' => 'adminmenu',
            'attributes' => [
                'required'   => true,
                'min'        => 1,
                'step'      => 1,
                'size'        => 40,
                'class'       => 'settings_adminmenu',
                'placeholder' => 'Admin menu posts per page',
                'value' => $this->config['adminmenu'],
            ],
            'options' => [
                'label' => 'Admin menu posts per page',
            ],
            ]
        );

        $this->add(
            [
            'type' => 'Zend\Form\Element\Number',
            'name' => 'administrator',
            'attributes' => [
                'required'   => true,
                'min'        => 1,
                'step'      => 1,
                'size'        => 40,
                'class'       => 'settings_administrator',
                'placeholder' => 'Administrator posts per page',
                'value' => $this->config['administrator'],
            ],
            'options' => [
                'label' => 'Administrator posts per page',
            ],
            ]
        );

        $this->add(
            [
            'type' => 'Zend\Form\Element\Number',
            'name' => 'language',
            'attributes' => [
                'required'   => true,
                'min'        => 1,
                'step'      => 1,
                'size'        => 40,
                'class'       => 'settings_language',
                'placeholder' => 'Language posts per page',
                'value' => $this->config['language'],
            ],
            'options' => [
                'label' => 'Language posts per page',
            ],
            ]
        );

        $this->add(
            [
            'type' => 'Zend\Form\Element\Number',
            'name' => 'user',
            'attributes' => [
                'required'   => true,
                'min'        => 1,
                'step'      => 1,
                'size'        => 40,
                'class'       => 'settings_user',
                'placeholder' => 'User posts per page',
                'value' => $this->config['user'],
            ],
            'options' => [
                'label' => 'User posts per page',
            ],
            ]
        );

        $this->add(
            [
            'type' => 'Zend\Form\Element\Number',
            'name' => 'news',
            'attributes' => [
                'required'   => true,
                'min'        => 1,
                'step'      => 1,
                'size'        => 40,
                'class'       => 'settings_news',
                'placeholder' => 'News posts per page',
                'value' => $this->config['news'],
            ],
            'options' => [
                'label' => 'News posts per page',
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
            ],
            ]
        );
    }

    public function getInputFilterSpecification()
    {
        return [
            [
                "name" => "content",
                "required" => true,
                'filters' => [
                    ['name' => 'Int'],
                ],
            ],
            [
                "name" => "menu",
                "required" => true,
                'filters' => [
                    ['name' => 'Int'],
                ],
            ],
            [
                "name" => "adminmenu",
                "required" => true,
                'filters' => [
                    ['name' => 'Int'],
                ],
            ],
            [
                "name" => "administrator",
                "required" => true,
                'filters' => [
                    ['name' => 'Int'],
                ],
            ],
            [
                "name" => "language",
                "required" => true,
                'filters' => [
                    ['name' => 'Int'],
                ],
            ],
            [
                "name" => "user",
                "required" => true,
                'filters' => [
                    ['name' => 'Int'],
                ],
            ],
            [
                "name" => "news",
                "required" => true,
                'filters' => [
                    ['name' => 'Int'],
                ],
            ],
        ];
    }
}
