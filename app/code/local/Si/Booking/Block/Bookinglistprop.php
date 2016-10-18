<?php
class Si_Booking_Block_Bookinglistprop extends Mage_Core_Block_Template
{
    public function __construct() {
        parent::__construct();
    	$hostId = Mage::getSingleton('customer/session')->getCustomer()->getId();
    	$collection = Mage::getModel('booking/booking')->getCollection()
    				->addFieldToFilter('host_id', $hostId)
    				->addFieldToFilter('product_id', $this->getRequest()->getParam('pid'));
    	$collection->getSelect()->order('created_at DESC');
        $this->setCollection($collection);
    }
    
    protected function _prepareLayout() {
        parent::_prepareLayout();
 
        $pager = $this->getLayout()->createBlock('page/html_pager', 'custom.pager');
        $pager->setAvailableLimit(array(5=>5,10=>10,15=>15,20=>20,'all'=>'all'));
        $pager->setShowPerPage(10);
        $pager->setCollection($this->getCollection());
        $this->setChild('pager', $pager);
        $this->getCollection()->load();
        return $this;
    }
 
    public function getPagerHtml() {
        return $this->getChildHtml('pager');
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
    				'3' => 'Refunded',
    				'4' => 'Cancelled by Guest'
    			);
    	return $status[$code];
    }
    public function getTitle() {
    	$product = Mage::getModel('catalog/product')->load($this->getRequest()->getParam('pid'));
    	return $product->getName();
    }
}
