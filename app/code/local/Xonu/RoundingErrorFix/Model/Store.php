<?php

/**
 * @copyright (c) 2013, Pawel Kazakow <support@xonu.de>
 * @license http://xonu.de/license/ xonu.de EULA
 */

class Xonu_RoundingErrorFix_Model_Store extends Mage_Core_Model_Store {

    private $classList = array();

    /**
     * Initialize object
     */
    protected function _construct()
    {
        $this->classList[] = get_class(Mage::getSingleton('tax/sales_total_quote_subtotal'));
        parent::_construct();
    }

    /**
     * Round price
     *
     * @param mixed $price
     * @return double
     */
    public function roundPrice($price)
    {
        $trace = debug_backtrace(); $depth = 2;

        // if(in_array($trace[$depth]['class'], $this->classList))
        if(isset($trace[$depth]['class']) && $trace[$depth]['class'] == $this->classList[0])
            $precision = 2;
        else
            $precision = 4;

        return round($price, 4);
    }
}