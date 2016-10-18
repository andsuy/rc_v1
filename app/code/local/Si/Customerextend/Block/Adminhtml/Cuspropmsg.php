<?php


class Si_Customerextend_Block_Adminhtml_Cuspropmsg extends Mage_Adminhtml_Block_Widget_Grid_Container{

	public function __construct()
	{

	$this->_controller = "adminhtml_cuspropmsg";
	$this->_blockGroup = "customerextend";
	$this->_headerText = Mage::helper("customerextend")->__("Cuspropmsg Manager");
	$this->_addButtonLabel = Mage::helper("customerextend")->__("Add New Item");
	parent::__construct();
	
	}

}