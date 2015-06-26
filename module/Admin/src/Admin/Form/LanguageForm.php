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
 * @category   Admin\Language
 * @package    Unnamed
 * @author     Stanimir Dimitrov <stanimirdim92@gmail.com>
 * @copyright  2015 Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 * @version    0.0.3
 * @link       TBA
 */

namespace Admin\Form;

use Zend\Form\Form;
use Zend\Form\Element;

class LanguageForm extends Form
{
    /**
     * Create the language form
     *
     * @param \Admin\Model\Language|null $options holds language options
     */
    public function __construct(\Admin\Model\Language $options = null)
    {
        parent::__construct("language");
        $elements = [];

        $elements[0] = new Element\Text('name');
        $elements[0]->setLabel('Name');
        $elements[0]->setAttributes([
            'required'   => true,
            'size'        => 40,
            'class'      => 'language-name',
            'placeholder' => 'Name',
        ]);
        if ($options!=null and $options->name) {
            $elements[0]->setValue($options->name);
        }

        $elements[1] = new Element\Checkbox('active');
        $elements[1]->setLabel('Active');
        if ($options!=null and $options->active) {
            $elements[1]->setValue($options->active);
        }

        $elements[2] = new Element\Submit('submit');
        $elements[2]->setAttribute('id', 'submitbutton');

        $elements[69] = new Element\Csrf('s');

        if ($options!=null) {
            $elements[3] = new Element\Hidden('id');
            $elements[3]->setValue($options->id);
        }

        foreach ($elements as $e) {
            $this->add($e);
        }
    }
}
