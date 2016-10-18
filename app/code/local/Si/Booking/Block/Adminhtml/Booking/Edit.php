<?php
	
class Si_Booking_Block_Adminhtml_Booking_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
		public function __construct()
		{

				parent::__construct();
				$this->_objectId = "booking_id";
				$this->_blockGroup = "booking";
				$this->_controller = "adminhtml_booking";
				$this->_updateButton("save", "label", Mage::helper("booking")->__("Save Item"));
				$this->_updateButton("delete", "label", Mage::helper("booking")->__("Delete Item"));

				$this->_addButton("saveandcontinue", array(
					"label"     => Mage::helper("booking")->__("Save And Continue Edit"),
					"onclick"   => "saveAndContinueEdit()",
					"class"     => "save",
				), -100);



				$this->_formScripts[] = "

							function saveAndContinueEdit(){
								editForm.submit($('edit_form').action+'back/edit/');
							}
						";
		}

		public function getHeaderText()
		{
				if( Mage::registry("booking_data") && Mage::registry("booking_data")->getId() ){

				    return Mage::helper("booking")->__("Edit Item '%s'", $this->htmlEscape(Mage::registry("booking_data")->getId()));

				} 
				else{

				     return Mage::helper("booking")->__("Add Item");

				}
		}
}