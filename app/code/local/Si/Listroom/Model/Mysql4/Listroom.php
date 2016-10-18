<?php
class Si_Listroom_Model_Mysql4_Listroom extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init("listroom/listroom", "listroom_id");
    }
}