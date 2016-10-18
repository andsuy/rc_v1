<?php
class Si_Property_Block_Average extends Mage_Core_Block_Template
{
    public function getAveragePrice($zipcode)
    {
    	$collection = Mage::getModel('catalog/product')->getCollection()
    				->addAttributeToFilter('zipcode', $zipcode);
    	if($collection->count()) {
    		$price = 0.00;
    		foreach($collection as $_product) {
    			$product = Mage::getModel('catalog/product')->load($_product->getId());
    			$price += $product->getPrice();
    		}
    		$averagePrice = (float)($price / $collection->count());
    		return $averagePrice;
    	} else {
    		return null;
    	}
    }
}
