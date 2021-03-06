<?php

class Si_Property_Adminhtml_PropertyavailablityController extends Mage_Adminhtml_Controller_Action
{
		protected function _initAction()
		{
				$this->loadLayout()->_setActiveMenu("property/propertyavailablity")->_addBreadcrumb(Mage::helper("adminhtml")->__("Propertyavailablity  Manager"),Mage::helper("adminhtml")->__("Propertyavailablity Manager"));
				return $this;
		}
		public function indexAction() 
		{
			    $this->_title($this->__("Property"));
			    $this->_title($this->__("Manager Propertyavailablity"));

				$this->_initAction();
				$this->renderLayout();
		}
		public function editAction()
		{			    
			    $this->_title($this->__("Property"));
				$this->_title($this->__("Propertyavailablity"));
			    $this->_title($this->__("Edit Item"));
				
				$id = $this->getRequest()->getParam("id");
				$model = Mage::getModel("property/propertyavailablity")->load($id);
				if ($model->getId()) {
					Mage::register("propertyavailablity_data", $model);
					$this->loadLayout();
					$this->_setActiveMenu("property/propertyavailablity");
					$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Propertyavailablity Manager"), Mage::helper("adminhtml")->__("Propertyavailablity Manager"));
					$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Propertyavailablity Description"), Mage::helper("adminhtml")->__("Propertyavailablity Description"));
					$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);
					$this->_addContent($this->getLayout()->createBlock("property/adminhtml_propertyavailablity_edit"))->_addLeft($this->getLayout()->createBlock("property/adminhtml_propertyavailablity_edit_tabs"));
					$this->renderLayout();
				} 
				else {
					Mage::getSingleton("adminhtml/session")->addError(Mage::helper("property")->__("Item does not exist."));
					$this->_redirect("*/*/");
				}
		}

		public function newAction()
		{

		$this->_title($this->__("Property"));
		$this->_title($this->__("Propertyavailablity"));
		$this->_title($this->__("New Item"));

        $id   = $this->getRequest()->getParam("id");
		$model  = Mage::getModel("property/propertyavailablity")->load($id);

		$data = Mage::getSingleton("adminhtml/session")->getFormData(true);
		if (!empty($data)) {
			$model->setData($data);
		}

		Mage::register("propertyavailablity_data", $model);

		$this->loadLayout();
		$this->_setActiveMenu("property/propertyavailablity");

		$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);

		$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Propertyavailablity Manager"), Mage::helper("adminhtml")->__("Propertyavailablity Manager"));
		$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Propertyavailablity Description"), Mage::helper("adminhtml")->__("Propertyavailablity Description"));


		$this->_addContent($this->getLayout()->createBlock("property/adminhtml_propertyavailablity_edit"))->_addLeft($this->getLayout()->createBlock("property/adminhtml_propertyavailablity_edit_tabs"));

		$this->renderLayout();

		}
		public function saveAction()
		{

			$post_data=$this->getRequest()->getPost();


				if ($post_data) {

					try {

						

						$model = Mage::getModel("property/propertyavailablity")
						->addData($post_data)
						->setId($this->getRequest()->getParam("id"))
						->save();

						Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Propertyavailablity was successfully saved"));
						Mage::getSingleton("adminhtml/session")->setPropertyavailablityData(false);

						if ($this->getRequest()->getParam("back")) {
							$this->_redirect("*/*/edit", array("id" => $model->getId()));
							return;
						}
						$this->_redirect("*/*/");
						return;
					} 
					catch (Exception $e) {
						Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
						Mage::getSingleton("adminhtml/session")->setPropertyavailablityData($this->getRequest()->getPost());
						$this->_redirect("*/*/edit", array("id" => $this->getRequest()->getParam("id")));
					return;
					}

				}
				$this->_redirect("*/*/");
		}



		public function deleteAction()
		{
				if( $this->getRequest()->getParam("id") > 0 ) {
					try {
						$model = Mage::getModel("property/propertyavailablity");
						$model->setId($this->getRequest()->getParam("id"))->delete();
						Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Item was successfully deleted"));
						$this->_redirect("*/*/");
					} 
					catch (Exception $e) {
						Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
						$this->_redirect("*/*/edit", array("id" => $this->getRequest()->getParam("id")));
					}
				}
				$this->_redirect("*/*/");
		}

		
		public function massRemoveAction()
		{
			try {
				$ids = $this->getRequest()->getPost('available_ids', array());
				foreach ($ids as $id) {
                      $model = Mage::getModel("property/propertyavailablity");
					  $model->setId($id)->delete();
				}
				Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Item(s) was successfully removed"));
			}
			catch (Exception $e) {
				Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
			}
			$this->_redirect('*/*/');
		}
			
		/**
		 * Export order grid to CSV format
		 */
		public function exportCsvAction()
		{
			$fileName   = 'propertyavailablity.csv';
			$grid       = $this->getLayout()->createBlock('property/adminhtml_propertyavailablity_grid');
			$this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
		} 
		/**
		 *  Export order grid to Excel XML format
		 */
		public function exportExcelAction()
		{
			$fileName   = 'propertyavailablity.xml';
			$grid       = $this->getLayout()->createBlock('property/adminhtml_propertyavailablity_grid');
			$this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
		}
}
