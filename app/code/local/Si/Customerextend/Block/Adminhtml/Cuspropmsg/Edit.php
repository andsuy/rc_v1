<?php
	
class Si_Customerextend_Block_Adminhtml_Cuspropmsg_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
		public function __construct()
		{

				parent::__construct();
				$this->_objectId = "prop_msg_id";
				$this->_blockGroup = "customerextend";
				$this->_controller = "adminhtml_cuspropmsg";
				$this->_updateButton("save", "label", Mage::helper("customerextend")->__("Save Item"));
				$this->_updateButton("delete", "label", Mage::helper("customerextend")->__("Delete Item"));

				$this->_addButton("saveandcontinue", array(
					"label"     => Mage::helper("customerextend")->__("Save And Continue Edit"),
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
				if( Mage::registry("cuspropmsg_data") && Mage::registry("cuspropmsg_data")->getId() ){

				    return Mage::helper("customerextend")->__("Edit Item '%s'", $this->htmlEscape(Mage::registry("cuspropmsg_data")->getId()));

				} 
				else{

				     return Mage::helper("customerextend")->__("Add Item");

				}
		}
}