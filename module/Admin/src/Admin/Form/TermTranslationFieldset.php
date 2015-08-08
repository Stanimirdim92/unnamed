<?php
/**
 * MIT License
 * ===========
 *
 * Copyright (c) 2015 Stanimir Dimitrov <stanimirdim92@gmail.com>
 *
 * Permission is hereby granted, free of charge, to any person obtaining
 * a copy of this software and associated documentation files (the
 * "Software"), to deal in the Software without restriction, including
 * without limitation the rights to use, copy, modify, merge, publish,
 * distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to
 * the following conditions:
 *
 * The above copyright notice and this permission notice shall be included
 * in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS
 * OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
 * IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY
 * CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT,
 * TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE
 * SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 *
 * @author     Stanimir Dimitrov <stanimirdim92@gmail.com>
 * @copyright  2015 (c) Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 * @version    0.0.6
 * @link       TBA
 */

namespace Admin\Form;


use Admin\Model\TermTranslation;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Stdlib\Hydrator\ClassMethods as ClassMethodsHydrator;

class TermTranslationFieldset extends Fieldset implements InputFilterProviderInterface
{
    public function __construct()
    {
        parent::__construct("termtranslationfieldset");

        $this
             ->setHydrator(new ClassMethodsHydrator(false))
             ->setObject(new TermTranslation())
         ;

        // foreach ($terms as $a) {
        //     if (isset($termTranslations[$a->id])) {
        //         $translation = $termTranslations[$a->id]->translation;
        //     } else {
        //         $translation = '';
        //     }
        //     if (strrpos(strtolower($a->name), '_text') > 0) {
        //         $elements[$i] = new Element\Textarea('translation' . $a->id);
        //         $elements[$i]->setLabel($a->name . ": ");
        //         $elements[$i]->setAttributes([
        //             'rows'        => 3,
        //             'cols'        => 80,
        //             'class'      => 'termtranslation-name',
        //             'placeholder' => 'Term translation',
        //         ]);
        //     } else {
        //         $elements[$i] = new Element\Text('translation' . $a->id);
        //         $elements[$i]->setLabel($a->name . ": ");
        //         $elements[0]->setAttributes([
        //             'size'        => 40,
        //             'class'      => 'termtranslation-name',
        //             'placeholder' => 'Term translation',
        //         ]);
        //     }
        //     $elements[$i++]->setValue($translation);
        // }

        $this->add([
             'type' => 'Zend\Form\Element\Collection',
             'name' => 'termName',
             'options' => [
                 'should_create_template' => true,
                 'template_placeholder' => '__element2Index__',
                 'allow_add' => true,
                 'target_element' => [
                     'type' => 'Admin\Form\TermFieldset',
                 ],
             ],
        ]);

        $this->add([
            'type' => 'Zend\Form\Element\Textarea',
            'name' => 'translation',
            'attributes' => [
                'required'   => true,
                'rows'        => 3,
                'cols'        => 80,
                'class'      => 'termtranslation-name',
                'placeholder' => 'Term translation',
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
            // [
            //     'name' => 'id',
            //     'required' => false,
            //     'filters' => [
            //         ['name' => 'Int'],
            //     ],
            // ],
            // [
            //     'name' => 'language',
            //     'required' => false,
            //     'filters' => [
            //         ['name' => 'Int'],
            //     ],
            // ],
            [
                "name"=>"translation",
                'required' => false,
                'filters' => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                ],
                'validators' => [
                    [
                        'name' => 'StringLength',
                        'options' => [
                            'encoding' => 'UTF-8',
                        ],
                    ],
                    ['name' => 'NotEmpty'],
                ],
            ],
            // [
            //     'name' => 'term',
            //     'required' => false,
            //     'filters' => [
            //         ['name' => 'Int'],
            //     ],
            // ],
        ];
    }
}
