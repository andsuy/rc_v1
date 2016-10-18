<?php
class Si_Property_Block_Adminhtml_Propertyavailablity_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
		protected function _prepareForm()
		{

				$form = new Varien_Data_Form();
				$this->setForm($form);
				$fieldset = $form->addFieldset("property_form", array("legend"=>Mage::helper("property")->__("Item information")));

				
						$fieldset->addField("product_id", "text", array(
						"label" => Mage::helper("property")->__("Product Id"),					
						"class" => "required-entry",
						"required" => true,
						"name" => "product_id",
						));
					
						$fieldset->addField("booking_type", "text", array(
						"label" => Mage::helper("property")->__("Booking Type"),
						"name" => "booking_type",
						));
					

				if (Mage::getSingleton("adminhtml/session")->getPropertyavailablityData())
				{
					$form->setValues(Mage::getSingleton("adminhtml/session")->getPropertyavailablityData());
					Mage::getSingleton("adminhtml/session")->setPropertyavailablityData(null);
				} 
				elseif(Mage::registry("propertyavailablity_data")) {
				    $form->setValues(Mage::registry("propertyavailablity_data")->getData());
				}
				return parent::_prepareForm();
		}
}
