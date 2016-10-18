<?php   
class Si_Booking_Block_View extends Mage_Core_Block_Template{   
    public function getBooking() {
    	$id = $this->getRequest()->getParam('id');
    	$booking = Mage::getModel('booking/booking')->load($id);
        return $booking;
    }
    
    public function getOrder($_oId) {
    	return Mage::getModel('sales/order')->load($_oId);
    }
    public function getProduct($_pId) {
    	return Mage::getModel('catalog/product')->load($_pId);
    }
    public function getGuest($_gId) {
    	return Mage::getModel('customer/customer')->load($_gId);
    }
    public function getBookingStatusLabel($code) {
    	$status = array(
    				'0' => 'Pending',
    				'1' => 'Confirmed',
    				'2' => 'Canceled',
    				'3' => 'Refunded'
    			);
    	return $status[$code];
    }
    public function getTitle($_pId) {
    	$product = Mage::getModel('catalog/product')->load($_pId);
    	return $product->getName();
    }
    public function getSecretCodeStatusLabel($code) {
    	$status = array(
    				'0' => 'Not Generated',
    				'1' => 'Sent to Client',
    				'2' => 'Confirmed by Host',
    				'3' => 'Blocked'
    			);
    	return $status[$code];
    }
}