<?php
class Excellence_Fee_Model_Sales_Quote_Address_Total_Deposit extends Mage_Sales_Model_Quote_Address_Total_Abstract{
	protected $_code = 'deposit';

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
		/*if(Excellence_Fee_Model_Fee::canApply($address)){
			$exist_amount = $quote->getDepositAmount();
			$deposit = Mage::getSingleton('core/session')->getDeposit();
			if(!empty($deposit)) {
				$fee = $deposit;
			}
			$rawFee = $fee - $exist_amount;
			$_store = Mage::app()->getStore();
			$balance = Mage::helper('directory')->currencyConvert($rawFee, Mage::app()->getStore()->getBaseCurrencyCode(), Mage::app()->getStore()->getCurrentCurrencyCode());
			// 			$balance = $fee;
			
			//$this->_setAmount($balance);
			//$this->_setBaseAmount($balance);

			$address->setDepositAmount($balance);
			$address->setBaseDepositAmount($rawFee);
				
			$quote->setDepositAmount($balance);

			$address->setGrandTotal($address->getGrandTotal() + $address->getDepositAmount());
			$address->setBaseGrandTotal($address->getBaseGrandTotal() + $address->getBaseDepositAmount());
		}*/
	}

	public function fetch(Mage_Sales_Model_Quote_Address $address)
	{
		/*$amt = $address->getDepositAmount();
		$address->addTotal(array(
				'code'=>$this->getCode(),
				'title'=>Mage::helper('fee')->__('Deposit Amount'),
				'value'=> $amt
		));*/
		return $this;
	}
}