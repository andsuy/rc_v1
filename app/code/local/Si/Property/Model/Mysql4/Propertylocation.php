<?php
class Si_Property_Model_Mysql4_Propertylocation extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init("property/propertylocation", "location_id");
    }
}