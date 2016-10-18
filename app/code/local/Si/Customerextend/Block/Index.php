<?php   
class Si_Customerextend_Block_Index extends Mage_Core_Block_Template {
    public function getCustomerProfile() {
    	$customer_id = Mage::getSingleton('customer/session')->getId();
        $customerProfile = Mage::getModel('customerextend/customerinfo')->load($customer_id, 'customer_id');
    	//echo '<pre>'; print_r($customerProfile); echo '</pre>'; exit;
    	return $customerProfile;
    }
}