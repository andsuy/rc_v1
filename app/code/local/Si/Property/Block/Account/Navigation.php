<?php
class Si_Property_Block_Account_Navigation extends Mage_Customer_Block_Account_Navigation {
	/**
	 * Remove the navigation links in frontend customer dashboard
	 * 
	 * @param array $name
	 * 
	 */
	public function removeLinkByName($name) {
		unset($this->_links[$name]);
	}
}