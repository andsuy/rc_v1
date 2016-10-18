<?php
class Si_Listroom_Block_Adminhtml_Listroom_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
		protected function _prepareForm()
		{

				$form = new Varien_Data_Form();
				$this->setForm($form);
				$fieldset = $form->addFieldset("listroom_form", array("legend"=>Mage::helper("listroom")->__("Item information")));

								
						 $fieldset->addField('user_id', 'select', array(
						'label'     => Mage::helper('listroom')->__('User Name'),
						'values'   => Si_Listroom_Block_Adminhtml_Listroom_Grid::getValueArray0(),
						'name' => 'user_id',					
						"class" => "required-entry",
						"required" => true,
						));				
						 $fieldset->addField('property_type', 'select', array(
						'label'     => Mage::helper('listroom')->__('Property Type'),
						'values'   => Si_Listroom_Block_Adminhtml_Listroom_Grid::getValueArray1(),
						'name' => 'property_type',					
						"class" => "required-entry",
						"required" => true,
						));
						$fieldset->addField("budget_min", "text", array(
						"label" => Mage::helper("listroom")->__("Budget Min/Night"),					
						"class" => "required-entry",
						"required" => true,
						"name" => "budget_min",
						));
					
						$fieldset->addField("budget_max", "text", array(
						"label" => Mage::helper("listroom")->__("Budget Max/Night"),					
						"class" => "required-entry",
						"required" => true,
						"name" => "budget_max",
						));
									
						 $fieldset->addField('room_type', 'select', array(
						'label'     => Mage::helper('listroom')->__('Room Type'),
						'values'   => Si_Listroom_Block_Adminhtml_Listroom_Grid::getValueArray4(),
						'name' => 'room_type',
						));
						$fieldset->addField("locality", "textarea", array(
						"label" => Mage::helper("listroom")->__("Preferred Locality"),					
						"class" => "required-entry",
						"required" => true,
						"name" => "locality",
						));
					
						$fieldset->addField("city", "text", array(
						"label" => Mage::helper("listroom")->__("City"),					
						"class" => "required-entry",
						"required" => true,
						"name" => "city",
						));
					
						$fieldset->addField("state", "text", array(
						"label" => Mage::helper("listroom")->__("State / Province / Region"),					
						"class" => "required-entry",
						"required" => true,
						"name" => "state",
						));
									
						 $fieldset->addField('country', 'select', array(
						'label'     => Mage::helper('listroom')->__('Country'),
						'values'   => Si_Listroom_Block_Adminhtml_Listroom_Grid::getValueArray8(),
						'name' => 'country',					
						"class" => "required-entry",
						"required" => true,
						));

				if (Mage::getSingleton("adminhtml/session")->getListroomData())
				{
					$form->setValues(Mage::getSingleton("adminhtml/session")->getListroomData());
					Mage::getSingleton("adminhtml/session")->setListroomData(null);
				} 
				elseif(Mage::registry("listroom_data")) {
				    $form->setValues(Mage::registry("listroom_data")->getData());
				}
				return parent::_prepareForm();
		}
}
