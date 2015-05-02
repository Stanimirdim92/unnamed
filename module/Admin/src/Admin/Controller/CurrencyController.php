<?php
namespace Admin\Controller;

use Admin\Model\Currency;
use Admin\Form\CurrencyForm;

class CurrencyController extends IndexController
{
    /**
     * Used to control the maximum number of the related objects in the forms
     *
     * @param Int $MAX_COUNT
     * @return Int
     */
    private $MAX_COUNT = 200;

    /**
     * @param string $NO_ID
     * @return string
     */
    protected $NO_ID = "no_id"; // const!!!

    /**
     * Initialize any variables before controller actions
     *
     * @param MvcEvent $e
     */
    public function onDispatch(\Zend\Mvc\MvcEvent $e)
    {
        $this->addBreadcrumb(array("reference"=>"/admin/currency", "name"=>"Currency"));
        parent::onDispatch($e);
    }

    /**
     * This action shows the list of all (or filtered) Currency objects
     */
    public function indexAction()
    {
        $order = "name ASC";
        $paginator = $this->getTable("currency")->fetchList(false, null, $order);
        $this->view->paginator = $paginator;
        return $this->view;
    }
    
    /**
     * This action serves for adding a new object of type Currency
     */
    // public function addAction()
    // {
    //     $this->showForm("Add", null);
    //     $this->addBreadcrumb(array("reference"=>"/admin/currency/add", "name"=>"Add new currency"));
    //     return $this->view;
    // }

    /**
     * This action presents a modify form for Currency object with a given id
     * Upon POST the form is processed and saved
     */
    public function modifyAction()
    {
        $id = $this->getParam("id", 0);
        if (!$id) {
            $this->setErrorNoParam($this->NO_ID);
            return $this->redirect()->toRoute('admin', array('controller' => 'currency'));
        }
        try {
            $currency = $this->getTable("currency")->getCurrency($id);
            $this->view->currency = $currency;
            $this->addBreadcrumb(array("reference"=>"/admin/currency/modify/id/{$currency->id}", "name"=>"Modify currency &laquo;".$currency->toString()."&raquo;"));
            $this->showForm("Modify", $currency);
        } catch (\Exception $ex) {
            $this->setErrorNoParam("Currency not found");
            return $this->redirect()->toRoute('admin', array('controller' => 'currency'));
        }
        return $this->view;
    }
    
    /**
     * This is common function used by add and modify actions (to avoid code duplication)
     *
     * @param String $label
     * @param null|Currency $currency
     */
    public function showForm($label = '', $currency = null)
    {
        if ($currency == null) {
            $currency = new Currency();
        }

        $form = new CurrencyForm($currency);

        $form->get("submit")->setValue($label);
        $this->view->form = $form;
        if ($this->getRequest()->isPost()) {
            $form->setInputFilter($currency->getInputFilter());
            $form->setData($this->getRequest()->getPost());
            if ($form->isValid()) {
                $currency->exchangeArray($form->getData());
                $this->getTable("currency")->saveCurrency($currency);
                $this->cache->success = $this->session->LANGUAGE."&nbsp;&laquo;".$currency->toString()."&raquo; ".$this->session->SAVE_SUCCESS;
                $this->view->setTerminal(true);
                return $this->redirect()->toRoute('admin', array('controller' => 'currency'));
            } else {
                $error = '';
                foreach ($form->getMessages() as $msg) {
                    foreach ($msg as $key => $value) {
                        $error = $value;
                    }
                }
                $this->setErrorNoParam($error);
                return $this->redirect()->toRoute('admin', array('controller' => 'currency'));
            }
        }
    }
    
    /**
     * this action deletes a currency object with a provided id
     */
    // public function deleteAction()
    // {
    //     $id = (int) $this->getParam('id', 0);
    //     if(!$id)
    //     {
    //         $this->setErrorNoParam($this->NO_ID);
    //         return $this->redirect()->toRoute('admin', array('controller' => 'currency'));
    //     }
    //     try
    //     {
    //         $this->getTable("currency")->deleteCurrency($id);
    //     }
    //     catch(\Exception $ex)
    //     {
    //         $this->setErrorNoParam("Currency not found");
    //         return $this->redirect()->toRoute('admin', array('controller' => 'currency'));
    //     }
    //     $this->cache->success = "Currency was successfully deleted";
    //     return $this->redirect()->toRoute('admin', array('controller' => 'currency'));
    // }

    public function detailAction()
    {
        $id = (int) $this->getParam('id', 0);
        if (!$id) {
            $this->setErrorNoParam($this->NO_ID);
            return $this->redirect()->toRoute('admin', array('controller' => 'currency'));
        }
        try {
            $curr = $this->getTable("Currency")->getCurrency($id);
            $this->view->curr = $curr;
        } catch (\Exception $ex) {
            $this->setErrorNoParam("Currency not found");
            return $this->redirect()->toRoute('admin', array('controller' => 'currency'));
        }
        $this->addBreadcrumb(array("reference"=>"/admin/currency/detail/id/{$curr->id}", "name"=>"currency &laquo;". $curr->toString()."&raquo; details"));
        return $this->view;
    }

    public function cloneAction()
    {
        $id = $this->getParam("id", 0);
        $currency = $this->getTable("currency")->duplicate($id);
        $this->cache->success = "Currency &laquo;".$currency->toString()."&raquo; was successfully cloned";
        $this->redirect()->toUrl("/admin/currency");
        return $this->view;
    }
}
