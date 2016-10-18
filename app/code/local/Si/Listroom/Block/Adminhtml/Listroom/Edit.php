<?php
	
class Si_Listroom_Block_Adminhtml_Listroom_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
		public function __construct()
		{

				parent::__construct();
				$this->_objectId = "listroom_id";
				$this->_blockGroup = "listroom";
				$this->_controller = "adminhtml_listroom";
				$this->_updateButton("save", "label", Mage::helper("listroom")->__("Save Item"));
				$this->_updateButton("delete", "label", Mage::helper("listroom")->__("Delete Item"));

				$this->_addButton("saveandcontinue", array(
					"label"     => Mage::helper("listroom")->__("Save And Continue Edit"),
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
				if( Mage::registry("listroom_data") && Mage::registry("listroom_data")->getId() ){

				    return Mage::helper("listroom")->__("Edit Item '%s'", $this->htmlEscape(Mage::registry("listroom_data")->getId()));

				} 
				else{

				     return Mage::helper("listroom")->__("Add Item");

				}
		}
}