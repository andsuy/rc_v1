<?php
class Si_Friendlist_Block_Adminhtml_Friendlist extends Mage_Adminhtml_Block_Widget_Grid_Container implements Mage_Adminhtml_Block_Widget_Tab_Interface {

	public function __construct()
	{
		$this->_controller = "adminhtml_friendlist";
		$this->_blockGroup = "friendlist";
		$this->_headerText = Mage::helper("friendlist")->__("Friendlist Manager");
		$this->_addButtonLabel = Mage::helper("friendlist")->__("Add New Item");
		parent::__construct();
		$this->_removeButton('add');
	}


    /**
     * Return Tab label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return $this->__('Friends List');
    }

    /**
     * Return Tab title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->__('Friends List');
    }

    /**
     * Can show tab in tabs
     *
     * @return boolean
     */
    public function canShowTab()
    {
        $customer = Mage::registry('current_customer');
        return (bool)$customer->getId();
    }

    /**
     * Tab is hidden
     *
     * @return boolean
     */
    public function isHidden()
    {
        return false;
    }

     /**
     * Defines after which tab, this tab should be rendered
     *
     * @return string
     */
    public function getAfter()
    {
        return 'tags';
    }
	
}