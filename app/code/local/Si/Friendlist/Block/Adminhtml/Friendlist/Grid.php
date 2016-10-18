<?php

class Si_Friendlist_Block_Adminhtml_Friendlist_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

		public function __construct()
		{
			parent::__construct();
			$this->setId("friendlistGrid");
			$this->setDefaultSort("friend_id");
			$this->setDefaultDir("ASC");
			$this->setSaveParametersInSession(true);
		}

		protected function _prepareCollection()
		{
			$collection = Mage::getModel("friendlist/friendlist")->getCollection()
							->addFieldToFilter('customer_id', $this->getRequest()->getParam('id'));
			$this->setCollection($collection);
			return parent::_prepareCollection();
		}
		protected function _prepareColumns()
		{
			/*$this->addColumn("friend_id", array(
				"header" => Mage::helper("friendlist")->__("ID"),
				"align" =>"right",
				"width" => "50px",
				"type" => "number",
				"index" => "friend_id",
			));*/
			
			$this->addColumn("friend_fb_id", array(
				"header" => Mage::helper("friendlist")->__("Friend Facebook Id"),
				"index" => "friend_fb_id",
			));
			
			$this->addColumn("friend_name", array(
				"header" => Mage::helper("friendlist")->__("Friend Name"),
				"index" => "friend_name",
			));
			
			$this->addExportType('friendlist/adminhtml_friendlist/exportCsv', Mage::helper('sales')->__('CSV')); 
			$this->addExportType('friendlist/adminhtml_friendlist/exportExcel', Mage::helper('sales')->__('Excel'));

			return parent::_prepareColumns();
		}

		public function getRowUrl($row)
		{
			return 'javascript:void(0)';
		}

}