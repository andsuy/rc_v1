<?php   
class Si_Property_Block_Form extends Mage_Core_Block_Template
{
	public function getRecommendedPrice() {
		if (Mage::getSingleton('customer/session')->isLoggedIn()) {
			$customerId = Mage::getSingleton('customer/session')->getCustomer()->getId();
			$propertyCollection = Mage::getModel('property/property')->getPropertyCollection()
									->addAttributeToSelect('user_id', $customerId);
			if($propertyCollection->count()) {
				$price = 0.00;
				$count = $propertyCollection->count();
				foreach ($propertyCollection as $_property) {
					$property = Mage::getModel('catalog/product')->load($_property->getId());
					$price += $property->getPrice();
				}
				return number_format((floor($price/$count)), 2);
			} else {
				return NULL;
			}
		} else {
			return NULL;
		}
	}
}