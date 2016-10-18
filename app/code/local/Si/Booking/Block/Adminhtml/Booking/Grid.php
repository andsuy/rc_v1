<?php

class Si_Booking_Block_Adminhtml_Booking_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

		public function __construct()
		{
				parent::__construct();
				$this->setId("bookingGrid");
				$this->setDefaultSort("booking_id");
				$this->setDefaultDir("DESC");
				$this->setSaveParametersInSession(true);
		}

		protected function _prepareCollection()
		{
				$collection = Mage::getModel("booking/booking")->getCollection();
				$this->setCollection($collection);
				return parent::_prepareCollection();
		}
		protected function _prepareColumns()
		{
				$this->addColumn("booking_id", array(
				"header" => Mage::helper("booking")->__("ID"),
				"align" =>"right",
				"width" => "50px",
			    "type" => "number",
				"index" => "booking_id",
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
			$this->setMassactionIdField('booking_id');
			$this->getMassactionBlock()->setFormFieldName('booking_ids');
			$this->getMassactionBlock()->setUseSelectAll(true);
			$this->getMassactionBlock()->addItem('remove_booking', array(
					 'label'=> Mage::helper('booking')->__('Remove Booking'),
					 'url'  => $this->getUrl('*/adminhtml_booking/massRemove'),
					 'confirm' => Mage::helper('booking')->__('Are you sure?')
				));
			return $this;
		}
			

}