<?php


class Si_Listroom_Block_Adminhtml_Listroom extends Mage_Adminhtml_Block_Widget_Grid_Container{

	public function __construct()
	{

	$this->_controller = "adminhtml_listroom";
	$this->_blockGroup = "listroom";
	$this->_headerText = Mage::helper("listroom")->__("Listroom Manager");
	$this->_addButtonLabel = Mage::helper("listroom")->__("Add New Item");
	parent::__construct();
	
	}

}