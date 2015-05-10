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
 * @category   Application\Login
 * @package    ZendPress
 * @author     Stanimir Dimitrov <stanimirdim92@gmail.com>
 * @copyright  2015 Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 * @version    0.03
 * @link       TBA
 */

namespace Application\Form;

use Zend\Form\Form;
use Zend\Form\Element;

class UserSettingsForm extends Form
{
    public function __construct($options = null)
    {
        parent::__construct("usersettings");
        $elements = [];

        $elements[0] = new Element\Text('name');
        $elements[0]->setAttributes([
            'required'   => false,
            'size'        => 40,
        ]);

        if ($options!=null and $options->name) {
            $elements[0]->setValue($options->name);
        }

        $elements[1] = new Element\Text('surname');
        $elements[1]->setAttributes([
            'required'   => false,
            'size'        => 40,
        ]);

        if ($options!=null and $options->surname) {
            $elements[1]->setValue($options->surname);
        }

        $elements[2] = new Element\Password("password");
        $elements[2]->setAttributes([
            // 'required'    => true,
            'size'        => 40,
            'placeholder' => '123456789',
        ]);

        $elements[4] = new Element\Text('email');
        $elements[4]->setAttributes([
            'required'   => true,
            'size'        => 40,
            'placeholder' => 'johnsmith@example.com',
        ]);

        if ($options!=null and $options->email) {
            $elements[4]->setValue($options->email);
        }

        $elements[5] = new Element\Text('birthDate');
        $elements[5]->setAttributes([
            'required'   => false,
            'size'        => 40,
            'placeholder' => 'YYYY-MM-DD',
        ]);
        if ($options!=null and $options->birthDate) {
            $elements[5]->setValue($options->birthDate);
        } else {
            $elements[5]->setValue("YYYY-MM-DD");
        }

        $elements[8] = new Element\Csrf('s');
        $elements[11] = new Element\Submit('submit');
        $elements[11]->setAttributea([
            'id' => 'submitbutton',
        ]);

        if ($options!=null) {
            $elements[12] = new Element\Hidden('id');
            $elements[12]->setValue($options->id);
        }

        foreach ($elements as $e) {
            $this->add($e);
        }
    }
}
