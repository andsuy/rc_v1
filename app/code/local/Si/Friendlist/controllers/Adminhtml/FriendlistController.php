<?php

class Si_Friendlist_Adminhtml_FriendlistController extends Mage_Adminhtml_Controller_Action
{
		protected function _initAction()
		{
				$this->loadLayout()->_setActiveMenu("friendlist/friendlist")->_addBreadcrumb(Mage::helper("adminhtml")->__("Friendlist  Manager"),Mage::helper("adminhtml")->__("Friendlist Manager"));
				return $this;
		}
		public function indexAction() 
		{
			    $this->_title($this->__("Friendlist"));
			    $this->_title($this->__("Manager Friendlist"));

				$this->_initAction();
				$this->renderLayout();
		}
		public function editAction()
		{			    
			    $this->_title($this->__("Friendlist"));
				$this->_title($this->__("Friendlist"));
			    $this->_title($this->__("Edit Item"));
				
				$id = $this->getRequest()->getParam("id");
				$model = Mage::getModel("friendlist/friendlist")->load($id);
				if ($model->getId()) {
					Mage::register("friendlist_data", $model);
					$this->loadLayout();
					$this->_setActiveMenu("friendlist/friendlist");
					$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Friendlist Manager"), Mage::helper("adminhtml")->__("Friendlist Manager"));
					$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Friendlist Description"), Mage::helper("adminhtml")->__("Friendlist Description"));
					$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);
					$this->_addContent($this->getLayout()->createBlock("friendlist/adminhtml_friendlist_edit"))->_addLeft($this->getLayout()->createBlock("friendlist/adminhtml_friendlist_edit_tabs"));
					$this->renderLayout();
				} 
				else {
					Mage::getSingleton("adminhtml/session")->addError(Mage::helper("friendlist")->__("Item does not exist."));
					$this->_redirect("*/*/");
				}
		}

		public function newAction()
		{

		$this->_title($this->__("Friendlist"));
		$this->_title($this->__("Friendlist"));
		$this->_title($this->__("New Item"));

        $id   = $this->getRequest()->getParam("id");
		$model  = Mage::getModel("friendlist/friendlist")->load($id);

		$data = Mage::getSingleton("adminhtml/session")->getFormData(true);
		if (!empty($data)) {
			$model->setData($data);
		}

		Mage::register("friendlist_data", $model);

		$this->loadLayout();
		$this->_setActiveMenu("friendlist/friendlist");

		$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);

		$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Friendlist Manager"), Mage::helper("adminhtml")->__("Friendlist Manager"));
		$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Friendlist Description"), Mage::helper("adminhtml")->__("Friendlist Description"));


		$this->_addContent($this->getLayout()->createBlock("friendlist/adminhtml_friendlist_edit"))->_addLeft($this->getLayout()->createBlock("friendlist/adminhtml_friendlist_edit_tabs"));

		$this->renderLayout();

		}
		public function saveAction()
		{

			$post_data=$this->getRequest()->getPost();


				if ($post_data) {

					try {

						

						$model = Mage::getModel("friendlist/friendlist")
						->addData($post_data)
						->setId($this->getRequest()->getParam("id"))
						->save();

						Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Friendlist was successfully saved"));
						Mage::getSingleton("adminhtml/session")->setFriendlistData(false);

						if ($this->getRequest()->getParam("back")) {
							$this->_redirect("*/*/edit", array("id" => $model->getId()));
							return;
						}
						$this->_redirect("*/*/");
						return;
					} 
					catch (Exception $e) {
						Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
						Mage::getSingleton("adminhtml/session")->setFriendlistData($this->getRequest()->getPost());
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
						$model = Mage::getModel("friendlist/friendlist");
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
				$ids = $this->getRequest()->getPost('friend_ids', array());
				foreach ($ids as $id) {
                      $model = Mage::getModel("friendlist/friendlist");
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
			$fileName   = 'friendlist.csv';
			$grid       = $this->getLayout()->createBlock('friendlist/adminhtml_friendlist_grid');
			$this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
		} 
		/**
		 *  Export order grid to Excel XML format
		 */
		public function exportExcelAction()
		{
			$fileName   = 'friendlist.xml';
			$grid       = $this->getLayout()->createBlock('friendlist/adminhtml_friendlist_grid');
			$this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
		}
}
