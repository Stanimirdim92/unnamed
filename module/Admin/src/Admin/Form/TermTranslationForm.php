<?php
namespace Admin\Form;
use Zend\Form\Form;
use Zend\Form\Element;

class TermTranslationForm extends Form
{
    public function __construct($options = null, $terms = [], $termTranslations = [])
    {
        parent::__construct("termTranslation");
        $elements = [];

        $i = 0;
        foreach ($terms as $a) {
            if (isset($termTranslations[$a->id])) {
                $translation = $termTranslations[$a->id]->translation;
            } else {
                $translation = '';
            }
            if (strrpos(strtolower($a->name), '_text') > 0) {
                $elements[$i] = new Element\Textarea('translation' . $a->id);
                $elements[$i]->setLabel($a->name . ": ");
                $elements[$i]->setAttributes([
                    'rows'        => 3,
                    'cols'        => 80,
                    'class'      => 'termtranslation-name',
                    'placeholder' => 'Term translation',
                ]);
            } else {
                $elements[$i] = new Element\Text('translation' . $a->id);
                $elements[$i]->setLabel($a->name . ": ");
                $elements[0]->setAttributes([
                    'size'        => 40,
                    'class'      => 'termtranslation-name',
                    'placeholder' => 'Term translation',
                ]);
            }
            $elements[$i++]->setValue($translation);
        }
        $elements[$i] = new Element\Submit('submit');
        $elements[$i++]->setAttribute('id', 'submitbutton')->setAttributes([
            'id' => 'submitbutton',
            'class' => 'termtranslation-button',
        ]);
        ;
        $elements[$i] = new Element\Hidden('id');
        $elements[$i]->setValue($options->id);
        foreach ($elements as $e) {
            $this->add($e);
        }
    }
}
