<?php
class Si_Listroom_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function getFormUrl()
	{
		return $this->_getUrl('booking/listing/form');
	}
	
	public function getPropertyName($propertyName)
    {
        $property_name = substr($propertyName, 0, 20);
        if(strlen($propertyName) >20) { $property_name.='..'; }
        return $property_name;
    }
}