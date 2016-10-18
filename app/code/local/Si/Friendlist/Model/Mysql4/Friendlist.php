<?php
class Si_Friendlist_Model_Mysql4_Friendlist extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init("friendlist/friendlist", "friend_id");
    }
}