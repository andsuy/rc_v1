<?php
class Excellence_Fee_Model_Sales_Quote_Address_Total_Fee extends Mage_Sales_Model_Quote_Address_Total_Abstract{
	protected $_code = 'fee';

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
			$exist_amount = $quote->getFeeAmount();
			$serviceFee = Mage::getSingleton('core/session')->getServiceFee();
			if(!empty($serviceFee)) {
				$fee = $serviceFee;
			} else {
				$fee = Excellence_Fee_Model_Fee::getFee();
			}
			$rawFee = $fee - $exist_amount;
			$_store = Mage::app()->getStore();
			$balance = Mage::helper('directory')->currencyConvert($rawFee, Mage::app()->getStore()->getBaseCurrencyCode(), Mage::app()->getStore()->getCurrentCurrencyCode());
			// 			$balance = $fee;
			
			//$this->_setAmount($balance);
			//$this->_setBaseAmount($balance);
		
			$address->setFeeAmount($balance);
			$address->setBaseFeeAmount($rawFee);
				
			$quote->setFeeAmount($balance);

			$address->setGrandTotal($address->getGrandTotal() + $address->getFeeAmount());
			$address->setBaseGrandTotal($address->getBaseGrandTotal() + $address->getBaseFeeAmount());
		}
	}

	public function fetch(Mage_Sales_Model_Quote_Address $address)
	{
		$amt = $address->getFeeAmount();
		$address->addTotal(array(
				'code'=>$this->getCode(),
				'title'=>Mage::helper('fee')->__('Processing Fee'),
				'value'=> $amt
		));
		return $this;
	}
}