<?php
class Si_Customerextend_IndexController extends Mage_Core_Controller_Front_Action{
    public function profileAction() {
        $customerid = Mage::app()->getRequest()->getParam('id');
        $customer = Mage::getModel('customer/customer')->load($customerid);
        if(Mage::getStoreConfig('property_section/layout_settings/hide_address')) {
        	$nameArray = explode(' ', $customer->getName());
        	$displayName = $nameArray[0];
        } else {
        	$displayName = $_user['name'];
        }
        $this->loadLayout();
        $this->getLayout()->getBlock('head')->setTitle($displayName);
        $this->renderLayout();
    }
}