<?php
	
class Si_Property_Block_Adminhtml_Propertyavailablity_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
		public function __construct()
		{

				parent::__construct();
				$this->_objectId = "available_id";
				$this->_blockGroup = "property";
				$this->_controller = "adminhtml_propertyavailablity";
				$this->_updateButton("save", "label", Mage::helper("property")->__("Save Item"));
				$this->_updateButton("delete", "label", Mage::helper("property")->__("Delete Item"));

				$this->_addButton("saveandcontinue", array(
					"label"     => Mage::helper("property")->__("Save And Continue Edit"),
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
				if( Mage::registry("propertyavailablity_data") && Mage::registry("propertyavailablity_data")->getId() ){

				    return Mage::helper("property")->__("Edit Item '%s'", $this->htmlEscape(Mage::registry("propertyavailablity_data")->getId()));

				} 
				else{

				     return Mage::helper("property")->__("Add Item");

				}
		}
}