<?php
class Si_Customerextend_Block_Adminhtml_Cuspropmsg_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
		protected function _prepareForm()
		{

				$form = new Varien_Data_Form();
				$this->setForm($form);
				$fieldset = $form->addFieldset("customerextend_form", array("legend"=>Mage::helper("customerextend")->__("Item information")));

				
						$fieldset->addField("prop_msg_id", "text", array(
						"label" => Mage::helper("customerextend")->__("Property Message Id"),					
						"class" => "required-entry",
						"required" => true,
						"name" => "prop_msg_id",
						));
					

				if (Mage::getSingleton("adminhtml/session")->getCuspropmsgData())
				{
					$form->setValues(Mage::getSingleton("adminhtml/session")->getCuspropmsgData());
					Mage::getSingleton("adminhtml/session")->setCuspropmsgData(null);
				} 
				elseif(Mage::registry("cuspropmsg_data")) {
				    $form->setValues(Mage::registry("cuspropmsg_data")->getData());
				}
				return parent::_prepareForm();
		}
}
