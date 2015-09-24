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
 * CLAIM, DAMAGES OR OTHER LIABILITY, WHE`HER IN AN ACTION OF CONTRACT,
 * TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE
 * SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 *
 * @author     Stanimir Dimitrov <stanimirdim92@gmail.com>
 * @copyright  2015 (c) Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 * @version    0.0.13
 * @link       TBA
 */

namespace Application\Controller;

use Application\Form\ContactForm;

final class ContactController extends IndexController
{
    /**
     * @var ContactForm $contactForm
     */
    private $contactForm = null;

    /**
     * @param Application\Form\ContactForm $contactForm
     */
    public function __construct(ContactForm $contactForm = null)
    {
        parent::__construct();
        $this->contactForm = $contactForm;
    }

    /**
     * Simple contact form
     */
    public function indexAction()
    {
        $this->getView()->setTemplate("application/contact/index");

        /**
         * @var $form ContactForm
         */
        $form = $this->contactForm;
        $form->get("email")->setLabel($this->translate("EMAIL"));
        $form->get("name")->setLabel($this->translate("NAME"))->setAttribute("placeholder", $this->translate("ENTER_NAME"));
        $form->get("subject")->setLabel($this->translate("SUBJECT"))->setAttribute("placeholder", $this->translate("ENTER_SUBJECT"));
        $form->get("captcha")->setLabel($this->translate("CAPTCHA"))->setAttribute("placeholder", $this->translate("ENTER_CAPTCHA"));
        $form->get("message")->setLabel($this->translate("MESSAGE"))->setAttribute("placeholder", $this->translate("ENTER_MESSAGE"));

        $this->getView()->form = $form;
        if ($this->getRequest()->isPost()) {
            $form->setInputFilter($form->getInputFilter());
            $form->setData($this->getRequest()->getPost());
            if ($form->isValid()) {
                $formData = $form->getData();
                try {
                    // must be set from db
                    $this->Mailing()->sendMail("psyxopat@gmail.com", '', $formData["subject"], $formData["message"], $formData["email"], $formData["name"]);
                    $this->setLayoutMessages($this->translate("CONTACT_SUCCESS"), 'success');
                } catch (\Exception $e) {
                    $this->setLayoutMessages($this->translate("CONTACT_ERROR"), 'error');
                }
            } else {
                $this->setLayoutMessages($form->getMessages(), 'error');
            }
        }
        return $this->getView();
    }
}
