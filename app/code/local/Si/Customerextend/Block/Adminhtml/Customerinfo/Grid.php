<?php

class Si_Customerextend_Block_Adminhtml_Customerinfo_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

		public function __construct()
		{
				parent::__construct();
				$this->setId("customerinfoGrid");
				$this->setDefaultSort("pc_id");
				$this->setDefaultDir("DESC");
				$this->setSaveParametersInSession(true);
		}

		protected function _prepareCollection()
		{
				$collection = Mage::getModel("customerextend/customerinfo")->getCollection();
				$this->setCollection($collection);
				return parent::_prepareCollection();
		}
		protected function _prepareColumns()
		{
				$this->addColumn("pc_id", array(
				"header" => Mage::helper("customerextend")->__("ID"),
				"align" =>"right",
				"width" => "50px",
			    "type" => "number",
				"index" => "pc_id",
				));
                
				$this->addColumn("customer_id", array(
				"header" => Mage::helper("customerextend")->__("Customer Id"),
				"index" => "customer_id",
				));
			$this->addExportType('*/*/exportCsv', Mage::helper('sales')->__('CSV')); 
			$this->addExportType('*/*/exportExcel', Mage::helper('sales')->__('Excel'));

				return parent::_prepareColumns();
		}

		public function getRowUrl($row)
		{
			   return $this->getUrl("*/*/edit", array("id" => $row->getId()));
		}


		
		protected function _prepareMassaction()
		{
			$this->setMassactionIdField('pc_id');
			$this->getMassactionBlock()->setFormFieldName('pc_ids');
			$this->getMassactionBlock()->setUseSelectAll(true);
			$this->getMassactionBlock()->addItem('remove_customerinfo', array(
					 'label'=> Mage::helper('customerextend')->__('Remove Customerinfo'),
					 'url'  => $this->getUrl('*/adminhtml_customerinfo/massRemove'),
					 'confirm' => Mage::helper('customerextend')->__('Are you sure?')
				));
			return $this;
		}
			

}