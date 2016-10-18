<?php
class Si_Customerextend_Model_Mysql4_Cuspropmsg extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init("customerextend/cuspropmsg", "prop_msg_id");
    }
}