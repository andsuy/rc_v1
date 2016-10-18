<?php

class Si_Property_Block_Adminhtml_Propertyavailablity_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

		public function __construct()
		{
				parent::__construct();
				$this->setId("propertyavailablityGrid");
				$this->setDefaultSort("available_id");
				$this->setDefaultDir("DESC");
				$this->setSaveParametersInSession(true);
		}

		protected function _prepareCollection()
		{
				$collection = Mage::getModel("property/propertyavailablity")->getCollection();
				$this->setCollection($collection);
				return parent::_prepareCollection();
		}
		protected function _prepareColumns()
		{
				$this->addColumn("available_id", array(
				"header" => Mage::helper("property")->__("ID"),
				"align" =>"right",
				"width" => "50px",
			    "type" => "number",
				"index" => "available_id",
				));
                
				$this->addColumn("product_id", array(
				"header" => Mage::helper("property")->__("Product Id"),
				"index" => "product_id",
				));
				$this->addColumn("booking_type", array(
				"header" => Mage::helper("property")->__("Booking Type"),
				"index" => "booking_type",
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
			$this->setMassactionIdField('available_id');
			$this->getMassactionBlock()->setFormFieldName('available_ids');
			$this->getMassactionBlock()->setUseSelectAll(true);
			$this->getMassactionBlock()->addItem('remove_propertyavailablity', array(
					 'label'=> Mage::helper('property')->__('Remove Propertyavailablity'),
					 'url'  => $this->getUrl('*/adminhtml_propertyavailablity/massRemove'),
					 'confirm' => Mage::helper('property')->__('Are you sure?')
				));
			return $this;
		}
			

}