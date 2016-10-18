<?php
class Si_Booking_Block_Adminhtml_Booking_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
		protected function _prepareForm()
		{

				$form = new Varien_Data_Form();
				$this->setForm($form);
				$fieldset = $form->addFieldset("booking_form", array("legend"=>Mage::helper("booking")->__("Item information")));

				
						$fieldset->addField("booking_id", "text", array(
						"label" => Mage::helper("booking")->__("Id"),
						"name" => "booking_id",
						));
					

				if (Mage::getSingleton("adminhtml/session")->getBookingData())
				{
					$form->setValues(Mage::getSingleton("adminhtml/session")->getBookingData());
					Mage::getSingleton("adminhtml/session")->setBookingData(null);
				} 
				elseif(Mage::registry("booking_data")) {
				    $form->setValues(Mage::registry("booking_data")->getData());
				}
				return parent::_prepareForm();
		}
}
