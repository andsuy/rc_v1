<?php


class Si_Booking_Block_Adminhtml_Booking extends Mage_Adminhtml_Block_Widget_Grid_Container{

	public function __construct()
	{

	$this->_controller = "adminhtml_booking";
	$this->_blockGroup = "booking";
	$this->_headerText = Mage::helper("booking")->__("Booking Manager");
	$this->_addButtonLabel = Mage::helper("booking")->__("Add New Item");
	parent::__construct();
	
	}

}