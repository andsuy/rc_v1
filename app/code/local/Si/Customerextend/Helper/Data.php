<?php
class Si_Customerextend_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function getCustomerProfileImage($customer_id) {
        $customerProfile = Mage::getModel('customerextend/customerinfo')->load($customer_id, 'customer_id');
    	//echo '<pre>'; print_r($customerProfile); echo '</pre>'; exit;
    	return $customerProfile->getImageName();
    }
	public function getCustomerProfileData($customer_id) {
        $customerProfile = Mage::getModel('customerextend/customerinfo')->load($customer_id, 'customer_id');
    	//echo '<pre>'; print_r($customerProfile); echo '</pre>'; exit;
    	return $customerProfile;
    }
	public function getUnreadInboxCount() {
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        $cusId = $customer->getId();
        
		$selectResult = Mage::getModel('customerextend/cuspropmsg')->getCollection()
						->addFieldToFilter('reciever_id', $cusId)
						->addFieldToFilter('reciever_read', 0)
						->addFieldToFilter('reciever_delete', 0);
						
        return $selectResult->count();
    }
}