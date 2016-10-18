<?php

class Excellence_Fee_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function formatFee($amount){
		return Mage::helper('fee')->__('Processing Fee');
	}
	public function formatCleaningfee($amount){
		return Mage::helper('fee')->__('Cleaning Fee');
	}
	public function formatExtrafee($amount){
		return Mage::helper('fee')->__('Extra Fees');
	}
	/*public function formatDeposit($amount){
		return Mage::helper('fee')->__('Deposit Amount');
	}*/
}