<?php
class Si_Customerextend_Block_Adminhtml_Customerinfo_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
		public function __construct()
		{
				parent::__construct();
				$this->setId("customerinfo_tabs");
				$this->setDestElementId("edit_form");
				$this->setTitle(Mage::helper("customerextend")->__("Item Information"));
		}
		protected function _beforeToHtml()
		{
				$this->addTab("form_section", array(
				"label" => Mage::helper("customerextend")->__("Item Information"),
				"title" => Mage::helper("customerextend")->__("Item Information"),
				"content" => $this->getLayout()->createBlock("customerextend/adminhtml_customerinfo_edit_tab_form")->toHtml(),
				));
				return parent::_beforeToHtml();
		}

}
