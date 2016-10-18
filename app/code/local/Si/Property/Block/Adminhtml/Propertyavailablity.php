<?php


class Si_Property_Block_Adminhtml_Propertyavailablity extends Mage_Adminhtml_Block_Widget_Grid_Container{

	public function __construct()
	{

	$this->_controller = "adminhtml_propertyavailablity";
	$this->_blockGroup = "property";
	$this->_headerText = Mage::helper("property")->__("Propertyavailablity Manager");
	$this->_addButtonLabel = Mage::helper("property")->__("Add New Item");
	parent::__construct();
	
	}

}