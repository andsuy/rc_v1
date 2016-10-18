<?php
class Excellence_Fee_Block_Sales_Order_Total extends Mage_Core_Block_Template
{
    /**
     * Get label cell tag properties
     *
     * @return string
     */
    public function getLabelProperties()
    {
        return $this->getParentBlock()->getLabelProperties();
    }

    /**
     * Get order store object
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        return $this->getParentBlock()->getOrder();
    }

    /**
     * Get totals source object
     *
     * @return Mage_Sales_Model_Order
     */
    public function getSource()
    {
        return $this->getParentBlock()->getSource();
    }

    /**
     * Get value cell tag properties
     *
     * @return string
     */
    public function getValueProperties()
    {
        return $this->getParentBlock()->getValueProperties();
    }

    /**
     * Initialize reward points totals
     *
     * @return Enterprise_Reward_Block_Sales_Order_Total
     */
    public function initTotals()
    {
        if ((float) $this->getOrder()->getBaseFeeAmount()) {
            $source = $this->getSource();
            $value  = $source->getFeeAmount();

            $this->getParentBlock()->addTotal(new Varien_Object(array(
                'code'   => 'fee',
                'strong' => false,
                'label'  => Mage::helper('fee')->formatFee($value),
                'value'  => $source instanceof Mage_Sales_Model_Order_Creditmemo ? - $value : $value
            )));
        }

        if ((float) $this->getOrder()->getBaseCleaningfeeAmount()) {
            $source = $this->getSource();
            $value  = $source->getCleaningfeeAmount();

            $this->getParentBlock()->addTotal(new Varien_Object(array(
                'code'   => 'cleaningfee',
                'strong' => false,
                'label'  => Mage::helper('fee')->formatCleaningfee($value),
                'value'  => $source instanceof Mage_Sales_Model_Order_Creditmemo ? - $value : $value
            )));
        }

        if ((float) $this->getOrder()->getBaseExtrafeeAmount()) {
            $source = $this->getSource();
            $value  = $source->getExtrafeeAmount();

            $this->getParentBlock()->addTotal(new Varien_Object(array(
                'code'   => 'extrafee',
                'strong' => false,
                'label'  => Mage::helper('fee')->formatExtrafee($value),
                'value'  => $source instanceof Mage_Sales_Model_Order_Creditmemo ? - $value : $value
            )));
        }
        
        /*if ((float) $this->getOrder()->getBaseDepositAmount()) {
            $source = $this->getSource();
            $value  = $source->getDepositAmount();

            $this->getParentBlock()->addTotal(new Varien_Object(array(
                'code'   => 'deposit',
                'strong' => false,
                'label'  => Mage::helper('fee')->formatDeposit($value),
                'value'  => $source instanceof Mage_Sales_Model_Order_Creditmemo ? - $value : $value
            )));
        }*/
        
        return $this;
    }
}
