<?php


class Si_Customerextend_Block_Adminhtml_Customerinfo extends Mage_Adminhtml_Block_Widget_Grid_Container{

	public function __construct()
	{

	$this->_controller = "adminhtml_customerinfo";
	$this->_blockGroup = "customerextend";
	$this->_headerText = Mage::helper("customerextend")->__("Customerinfo Manager");
	$this->_addButtonLabel = Mage::helper("customerextend")->__("Add New Item");
	parent::__construct();
	
	}

}