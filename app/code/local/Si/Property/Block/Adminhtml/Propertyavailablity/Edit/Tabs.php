<?php
class Si_Property_Block_Adminhtml_Propertyavailablity_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
		public function __construct()
		{
				parent::__construct();
				$this->setId("propertyavailablity_tabs");
				$this->setDestElementId("edit_form");
				$this->setTitle(Mage::helper("property")->__("Item Information"));
		}
		protected function _beforeToHtml()
		{
				$this->addTab("form_section", array(
				"label" => Mage::helper("property")->__("Item Information"),
				"title" => Mage::helper("property")->__("Item Information"),
				"content" => $this->getLayout()->createBlock("property/adminhtml_propertyavailablity_edit_tab_form")->toHtml(),
				));
				return parent::_beforeToHtml();
		}

}
