<?php   
class Si_Listroom_Block_Edit extends Mage_Core_Block_Template{
	public function getRoom() {
		$id = $this->getRequest()->getParam('id');
		$room = Mage::getModel('listroom/listroom')->load($id);
		return $room;
	}
}