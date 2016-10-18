<?php
class Excellence_Fee_Model_Sales_Quote_Address_Total_Cleaningfee extends Mage_Sales_Model_Quote_Address_Total_Abstract{
	protected $_code = 'cleaningfee';

	public function collect(Mage_Sales_Model_Quote_Address $address)
	{
		parent::collect($address);
		
		$this->_setAmount(0);
		$this->_setBaseAmount(0);

		$items = $this->_getAddressItems($address);
		if (!count($items)) {
			return $this; //this makes only address type shipping to come through
		}


		$quote = $address->getQuote();
		if(Excellence_Fee_Model_Fee::canApply($address)){
			$exist_amount = $quote->getCleaningfeeAmount();
			$cleaningFee = Mage::getSingleton('core/session')->getCleaningFee();
			if(!empty($cleaningFee)) {
				$fee = $cleaningFee;
			} else {
				$fee = 0;
			}
			$rawFee = $fee - $exist_amount;
			$_store = Mage::app()->getStore();
			$balance = Mage::helper('directory')->currencyConvert($rawFee, Mage::app()->getStore()->getBaseCurrencyCode(), Mage::app()->getStore()->getCurrentCurrencyCode());
			// 			$balance = $fee;
			
			//$this->_setAmount($balance);
			//$this->_setBaseAmount($balance);

			$address->setCleaningfeeAmount($balance);
			$address->setBaseCleaningfeeAmount($rawFee);
				
			$quote->setCleaningfeeAmount($balance);

			$address->setGrandTotal($address->getGrandTotal() + $address->getCleaningfeeAmount());
			$address->setBaseGrandTotal($address->getBaseGrandTotal() + $address->getBaseCleaningfeeAmount());
		}
	}

	public function fetch(Mage_Sales_Model_Quote_Address $address)
	{
		$amt = $address->getCleaningfeeAmount();
		$address->addTotal(array(
				'code'=>$this->getCode(),
				'title'=>Mage::helper('fee')->__('Cleaning Fee'),
				'value'=> $amt
		));
		return $this;
	}
}