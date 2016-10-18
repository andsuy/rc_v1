<?php

class Si_Booking_Adminhtml_BookingController extends Mage_Adminhtml_Controller_Action
{
		protected function _initAction()
		{
				$this->loadLayout()->_setActiveMenu("booking/booking")->_addBreadcrumb(Mage::helper("adminhtml")->__("Booking  Manager"),Mage::helper("adminhtml")->__("Booking Manager"));
				return $this;
		}
		public function indexAction() 
		{
			    $this->_title($this->__("Booking"));
			    $this->_title($this->__("Manager Booking"));

				$this->_initAction();
				$this->renderLayout();
		}
		public function editAction()
		{			    
			    $this->_title($this->__("Booking"));
				$this->_title($this->__("Booking"));
			    $this->_title($this->__("Edit Item"));
				
				$id = $this->getRequest()->getParam("id");
				$model = Mage::getModel("booking/booking")->load($id);
				if ($model->getId()) {
					Mage::register("booking_data", $model);
					$this->loadLayout();
					$this->_setActiveMenu("booking/booking");
					$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Booking Manager"), Mage::helper("adminhtml")->__("Booking Manager"));
					$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Booking Description"), Mage::helper("adminhtml")->__("Booking Description"));
					$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);
					$this->_addContent($this->getLayout()->createBlock("booking/adminhtml_booking_edit"))->_addLeft($this->getLayout()->createBlock("booking/adminhtml_booking_edit_tabs"));
					$this->renderLayout();
				} 
				else {
					Mage::getSingleton("adminhtml/session")->addError(Mage::helper("booking")->__("Item does not exist."));
					$this->_redirect("*/*/");
				}
		}

		public function newAction()
		{

		$this->_title($this->__("Booking"));
		$this->_title($this->__("Booking"));
		$this->_title($this->__("New Item"));

        $id   = $this->getRequest()->getParam("id");
		$model  = Mage::getModel("booking/booking")->load($id);

		$data = Mage::getSingleton("adminhtml/session")->getFormData(true);
		if (!empty($data)) {
			$model->setData($data);
		}

		Mage::register("booking_data", $model);

		$this->loadLayout();
		$this->_setActiveMenu("booking/booking");

		$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);

		$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Booking Manager"), Mage::helper("adminhtml")->__("Booking Manager"));
		$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Booking Description"), Mage::helper("adminhtml")->__("Booking Description"));


		$this->_addContent($this->getLayout()->createBlock("booking/adminhtml_booking_edit"))->_addLeft($this->getLayout()->createBlock("booking/adminhtml_booking_edit_tabs"));

		$this->renderLayout();

		}
		public function saveAction()
		{

			$post_data=$this->getRequest()->getPost();


				if ($post_data) {

					try {

						

						$model = Mage::getModel("booking/booking")
						->addData($post_data)
						->setId($this->getRequest()->getParam("id"))
						->save();

						Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Booking was successfully saved"));
						Mage::getSingleton("adminhtml/session")->setBookingData(false);

						if ($this->getRequest()->getParam("back")) {
							$this->_redirect("*/*/edit", array("id" => $model->getId()));
							return;
						}
						$this->_redirect("*/*/");
						return;
					} 
					catch (Exception $e) {
						Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
						Mage::getSingleton("adminhtml/session")->setBookingData($this->getRequest()->getPost());
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
						$model = Mage::getModel("booking/booking");
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
				$ids = $this->getRequest()->getPost('booking_ids', array());
				foreach ($ids as $id) {
                      $model = Mage::getModel("booking/booking");
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
			$fileName   = 'booking.csv';
			$grid       = $this->getLayout()->createBlock('booking/adminhtml_booking_grid');
			$this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
		} 
		/**
		 *  Export order grid to Excel XML format
		 */
		public function exportExcelAction()
		{
			$fileName   = 'booking.xml';
			$grid       = $this->getLayout()->createBlock('booking/adminhtml_booking_grid');
			$this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
		}
}
