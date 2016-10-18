<?php
class Si_Customerextend_Model_Mysql4_Customerinfo extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init("customerextend/customerinfo", "pc_id");
    }
}