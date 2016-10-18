<?php
class Si_Booking_Block_Adminhtml_Booking_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
		public function __construct()
		{
				parent::__construct();
				$this->setId("booking_tabs");
				$this->setDestElementId("edit_form");
				$this->setTitle(Mage::helper("booking")->__("Item Information"));
		}
		protected function _beforeToHtml()
		{
				$this->addTab("form_section", array(
				"label" => Mage::helper("booking")->__("Item Information"),
				"title" => Mage::helper("booking")->__("Item Information"),
				"content" => $this->getLayout()->createBlock("booking/adminhtml_booking_edit_tab_form")->toHtml(),
				));
				return parent::_beforeToHtml();
		}

}
