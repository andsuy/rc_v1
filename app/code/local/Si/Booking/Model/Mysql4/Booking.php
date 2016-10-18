<?php
class Si_Booking_Model_Mysql4_Booking extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init("booking/booking", "booking_id");
    }
}