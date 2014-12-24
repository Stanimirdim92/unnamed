<?php
namespace Application\Controller;

use Application\Controller\IndexController;

use Custom\Plugins\Functions;
use Application\Form\ShopForm;
use Admin\Model\Purchase;
use Application\Form\UserSettingsForm;

class ShopController extends IndexController
{
    public function onDispatch(\Zend\Mvc\MvcEvent $e)
    {
        parent::onDispatch($e);
    }

    public function packetAction()
    {
        $id = $this->getParam('id');
        $packet = $this->getTable("packet")->getPacket($id);
        $this->view->packet = $packet;

        // euro
        if ($this->valuta->currency == 2)
        {
            $price = $packet->price * $packet->getEuro();
        }
        // dollar
        else if ($this->valuta->currency == 3)
        {
            $price = $packet->price * $packet->getDollar();
        }
        // pound
        else
        {
            $price = $packet->price;
        }
        $this->view->packetPrice = $price;


        if ($packet->getDiscount() > 0)
        {
            $discountPrice = ($packet->getDiscount() / 100) * $price;
            $price = number_format($price - $discountPrice, 2);
        }

        $uid = 0;
        if (isset($this->cache->user)) {
            $uid = $this->cache->user->id;
        }

        $form = new ShopForm($uid, $packet->id);
        $form->get("submit-order")->setValue($this->session->CONFIRM);
        $this->view->form = $form;
        if ($this->getRequest()->isPost())
        {
            $form->setData($this->getRequest()->getPost());
            $formData = $this->getRequest()->getPost();
            $totalPrice = $formData['packetexpires'] * $price;
            if (isset($formData['submit-order']))
            {
                $this->view->totalPrice = number_format($totalPrice, 2);
                $this->view->period = $formData['packetexpires'];
                $this->view->date = $formData['date'];
            }
            else if($form->isValid())
            {
                $formData = $form->getData();
                $purchase = new Purchase();
                $purchase->setUser($formData['uid']);
                $purchase->setPacket($formData['pid']);
                $purchase->setPacketExpireMonths($formData['packetexpires']);
                $purchase->setPurchaseDate($formData['date']);
                $purchase->setCurrency($this->valuta->currency);
                $purchase->setMoney(number_format($totalPrice, 2));
                $this->getTable("purchase")->savePurchase($purchase);
                $this->cache->success = $this->session->PURCHASE_SUCCESS;
                $this->cache->success = $this->session->PURCHASE_SUCCESS_TEXT;
                $this->view->setTerminal(true);
                $this->redirect()->toUrl("/shop/payment");
            }
        }
        $this->view->webTitle = "SEO packet / ".$packet->getTypeByName();
        return $this->view;
    }

    public function paymentAction()
    {
        if (!isset($this->cache->user) || !$this->cache->logged)
        {
            $this->redirect()->toUrl("/");
        }
        return $this->view;
    }
}
?>