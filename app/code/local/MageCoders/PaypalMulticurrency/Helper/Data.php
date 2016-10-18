<?php
/**
 * @author   MageCoders
 * @package    MageCoders_PaypalMulticurrency 
 */
class MageCoders_PaypalMulticurrency_Helper_Data extends Mage_Core_Helper_Abstract{
	
	public function convertCurrency($price,$currency = null){
		 
		if(!$currency){
			$currency = Mage::getSingleton('core/session')->getExCurrency();
		}  
		
		if(empty($currency)){
			return $price;
		}
		
		$baseCurrency = Mage::getSingleton('core/session')->getActiveCurrency();	
		if(empty($baseCurrency)){
			$baseCurrency = Mage::app()->getBaseCurrencyCode();
		}
		$rate = Mage::getResourceModel('directory/currency')->getRate($baseCurrency,$currency);
		$newPrice = (float)($price/$rate);
		
		return $newPrice;
		
	}
		
}