<?php

class Si_Listroom_Block_Adminhtml_Listroom_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

		public function __construct()
		{
				parent::__construct();
				$this->setId("listroomGrid");
				$this->setDefaultSort("listroom_id");
				$this->setDefaultDir("DESC");
				$this->setSaveParametersInSession(true);
		}

		protected function _prepareCollection()
		{
				$collection = Mage::getModel("listroom/listroom")->getCollection();
				$this->setCollection($collection);
				return parent::_prepareCollection();
		}
		protected function _prepareColumns()
		{
				$this->addColumn("listroom_id", array(
				"header" => Mage::helper("listroom")->__("ID"),
				"align" =>"right",
				"width" => "50px",
			    "type" => "number",
				"index" => "listroom_id",
				));
                
						$this->addColumn('user_id', array(
						'header' => Mage::helper('listroom')->__('User Name'),
						'index' => 'user_id',
						'type' => 'options',
						'options'=>Si_Listroom_Block_Adminhtml_Listroom_Grid::getOptionArray0(),				
						));
						
						$this->addColumn('property_type', array(
						'header' => Mage::helper('listroom')->__('Property Type'),
						'index' => 'property_type',
						'type' => 'options',
						'options'=>Si_Listroom_Block_Adminhtml_Listroom_Grid::getOptionArray1(),				
						));
						
				$this->addColumn("budget_min", array(
				"header" => Mage::helper("listroom")->__("Budget Min/Night"),
				"index" => "budget_min",
				));
				$this->addColumn("budget_max", array(
				"header" => Mage::helper("listroom")->__("Budget Max/Night"),
				"index" => "budget_max",
				));
						$this->addColumn('room_type', array(
						'header' => Mage::helper('listroom')->__('Room Type'),
						'index' => 'room_type',
						'type' => 'options',
						'options'=>Si_Listroom_Block_Adminhtml_Listroom_Grid::getOptionArray4(),				
						));
						
				$this->addColumn("city", array(
				"header" => Mage::helper("listroom")->__("City"),
				"index" => "city",
				));
				$this->addColumn("state", array(
				"header" => Mage::helper("listroom")->__("State / Province / Region"),
				"index" => "state",
				));
						$this->addColumn('country', array(
						'header' => Mage::helper('listroom')->__('Country'),
						'index' => 'country',
						'type' => 'options',
						'options'=>Si_Listroom_Block_Adminhtml_Listroom_Grid::getOptionArray8(),				
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
			$this->setMassactionIdField('listroom_id');
			$this->getMassactionBlock()->setFormFieldName('listroom_ids');
			$this->getMassactionBlock()->setUseSelectAll(true);
			$this->getMassactionBlock()->addItem('remove_listroom', array(
					 'label'=> Mage::helper('listroom')->__('Remove Listroom'),
					 'url'  => $this->getUrl('*/adminhtml_listroom/massRemove'),
					 'confirm' => Mage::helper('listroom')->__('Are you sure?')
				));
			return $this;
		}
			
		static public function getOptionArray0()
		{
            $data_array=array(); 
			$data_array[0]='Sandipan';
			$data_array[1]='Manoj';
            return($data_array);
		}
		static public function getValueArray0()
		{
            $data_array=array();
			foreach(Si_Listroom_Block_Adminhtml_Listroom_Grid::getOptionArray0() as $k=>$v){
               $data_array[]=array('value'=>$k,'label'=>$v);		
			}
            return($data_array);

		}
		
		static public function getOptionArray1()
		{
            $data_array=array(); 
			$data_array[0]='Apartment';
			$data_array[1]='House';
            return($data_array);
		}
		static public function getValueArray1()
		{
            $data_array=array();
			foreach(Si_Listroom_Block_Adminhtml_Listroom_Grid::getOptionArray1() as $k=>$v){
               $data_array[]=array('value'=>$k,'label'=>$v);		
			}
            return($data_array);

		}
		
		static public function getOptionArray4()
		{
            $data_array=array(); 
			$data_array[0]='Private';
			$data_array[1]='Shared';
            return($data_array);
		}
		static public function getValueArray4()
		{
            $data_array=array();
			foreach(Si_Listroom_Block_Adminhtml_Listroom_Grid::getOptionArray4() as $k=>$v){
               $data_array[]=array('value'=>$k,'label'=>$v);		
			}
            return($data_array);

		}
		
		static public function getOptionArray8()
		{
            $data_array=array(); 
			$data_array[0]='US';
			$data_array[1]='UK';
            return($data_array);
		}
		static public function getValueArray8()
		{
            $data_array=array();
			foreach(Si_Listroom_Block_Adminhtml_Listroom_Grid::getOptionArray8() as $k=>$v){
               $data_array[]=array('value'=>$k,'label'=>$v);		
			}
            return($data_array);

		}
		

}