<?php
class Si_Property_Model_Mysql4_Propertyspecial extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init("property/propertyspecial", "special_id");
    }
}