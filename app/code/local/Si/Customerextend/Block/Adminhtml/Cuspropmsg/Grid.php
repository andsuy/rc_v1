<?php

class Si_Customerextend_Block_Adminhtml_Cuspropmsg_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

		public function __construct()
		{
				parent::__construct();
				$this->setId("cuspropmsgGrid");
				$this->setDefaultSort("prop_msg_id");
				$this->setDefaultDir("DESC");
				$this->setSaveParametersInSession(true);
		}

		protected function _prepareCollection()
		{
				$collection = Mage::getModel("customerextend/cuspropmsg")->getCollection();
				$this->setCollection($collection);
				return parent::_prepareCollection();
		}
		protected function _prepareColumns()
		{
				$this->addColumn("prop_msg_id", array(
				"header" => Mage::helper("customerextend")->__("ID"),
				"align" =>"right",
				"width" => "50px",
			    "type" => "number",
				"index" => "prop_msg_id",
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
			$this->setMassactionIdField('prop_msg_id');
			$this->getMassactionBlock()->setFormFieldName('prop_msg_ids');
			$this->getMassactionBlock()->setUseSelectAll(true);
			$this->getMassactionBlock()->addItem('remove_cuspropmsg', array(
					 'label'=> Mage::helper('customerextend')->__('Remove Cuspropmsg'),
					 'url'  => $this->getUrl('*/adminhtml_cuspropmsg/massRemove'),
					 'confirm' => Mage::helper('customerextend')->__('Are you sure?')
				));
			return $this;
		}
			

}