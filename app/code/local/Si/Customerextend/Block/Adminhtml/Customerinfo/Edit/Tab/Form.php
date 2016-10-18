<?php
class Si_Customerextend_Block_Adminhtml_Customerinfo_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
		protected function _prepareForm()
		{

				$form = new Varien_Data_Form();
				$this->setForm($form);
				$fieldset = $form->addFieldset("customerextend_form", array("legend"=>Mage::helper("customerextend")->__("Item information")));

				
						$fieldset->addField("customer_id", "text", array(
						"label" => Mage::helper("customerextend")->__("Customer Id"),					
						"class" => "required-entry",
						"required" => true,
						"name" => "customer_id",
						));
					

				if (Mage::getSingleton("adminhtml/session")->getCustomerinfoData())
				{
					$form->setValues(Mage::getSingleton("adminhtml/session")->getCustomerinfoData());
					Mage::getSingleton("adminhtml/session")->setCustomerinfoData(null);
				} 
				elseif(Mage::registry("customerinfo_data")) {
				    $form->setValues(Mage::registry("customerinfo_data")->getData());
				}
				return parent::_prepareForm();
		}
}
