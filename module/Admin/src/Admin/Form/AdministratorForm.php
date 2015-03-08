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
 * @category   Admin\Administrator
 * @package    ZendPress
 * @author     Stanimir Dimitrov <stanimirdim92@gmail.com>
 * @copyright  2015 Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 * @version    0.03
 * @link       TBA
 */
namespace Admin\Form;

use Zend\Form\Form;
use Zend\Form\Element;

class AdministratorForm extends Form
{
    /**
     * Create administrator form
     *
     * @param \Admin\Model\Administrator|null $options [description]
     */
    public function __construct(\Admin\Model\Administrator $options = null)
    {
        parent::__construct("administrator");
        $elements = array();

        $elements[5] = new Element\Text('user');
        $elements[5]->setLabel('User ID');
        $elements[5]->setAttributes(array(
            'required'   => true,
            'size'        => 40,
            'class'      => 'administrator-user ajax-search',
            'placeholder' => 'User ID',
            'autocomplete' => "off"
        ));

        if($options!=null and $options->user)
            $elements[5]->setValue($options->user);

        $elements[69] = new Element\Csrf('s');
        $elements[111] = new Element\Submit('submit');
        $elements[111]->setAttribute('id', 'submitbutton');

        if($options!=null)
        {
            $elements[112] = new Element\Hidden('id');
            $elements[112]->setValue($options->id);
        }

        foreach($elements as $e)
        {
            $this->add($e);
        }
    }
}
