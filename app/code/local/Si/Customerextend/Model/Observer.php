<?php
class Si_Customerextend_Model_Observer
{
	public function saveCurrencyChoice(Varien_Event_Observer $observer)
	{
		if($currency = Mage::app()->getRequest()->getParam('currency')) {
			$customerId = $observer->getEvent()->getCustomer()->getId();
			$data = array(
						'customer_id' => $customerId,
						'currency'	  => $currency,
						'created_at'  => now(),
						'update_at'	  => now()
					);
			$model = Mage::getModel('customerextend/customerinfo');
			$model->setData($data)->save();
			Mage::app()->getStore()->setCurrentCurrencyCode($currency);
		}
	}
	public function setCurrency(Varien_Event_Observer $observer)
	{
		$customerId = $observer->getEvent()->getCustomer()->getId();
		$customerObj = Mage::getModel('customerextend/customerinfo')->load($customerId, 'customer_id');
		if($customerObj->getCurrency()) {
			Mage::app()->getStore()->setCurrentCurrencyCode($customerObj->getCurrency());
		} else {
			Mage::app()->getStore()->setCurrentCurrencyCode('USD');
		}
	}

	public function clearAbandonedCarts(Varien_Event_Observer $observer)
    {
        $lastQuoteId = Mage::getSingleton('checkout/session')->getQuoteId();
        if ($lastQuoteId) {
            $customerQuote = Mage::getModel('sales/quote')
                ->loadByCustomer(Mage::getSingleton('customer/session')->getCustomerId());
            $customerQuote->setQuoteId($lastQuoteId);           
            $this->_removeAllItems($customerQuote);
 
        } else {
            $quote = Mage::getModel('checkout/session')->getQuote();
            $this->_removeAllItems($quote);
        }
    }
 
    protected function _removeAllItems($quote){
        foreach ($quote->getAllItems() as $item) {
            $item->isDeleted(true);
            if ($item->getHasChildren()) {
                foreach ($item->getChildren() as $child) {
                    $child->isDeleted(true);
                }
            }
        }
        $quote->collectTotals()->save();
    }
}