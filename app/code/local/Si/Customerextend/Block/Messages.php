<?php
class Si_Customerextend_Block_Messages extends Mage_Core_Block_Template
{
    protected function _prepareLayout()
    {
        return parent::_prepareLayout();
    }
    
    public function getUsers() {
    	$userIdArray = array();
    	$customerDeatils = array();
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        $cusId = $customer->getId();
        
        $messageCollection = Mage::getModel('customerextend/cuspropmsg')->getCollection()
        					->addFieldToFilter(array('reciever_id', 'sender_id'), array($cusId, $cusId));
        $messageCollection->getSelect()->order('created_at DESC');
        if($messageCollection->count() > 0) {
        	foreach($messageCollection as $_res) {
        		if($_res->getSenderId() != $cusId) { $userIdArray[] = $_res->getSenderId(); }
        		if($_res->getRecieverId() != $cusId) { $userIdArray[] = $_res->getRecieverId(); }
        	}
        }
        $uniqueUserIdArray = array_unique($userIdArray);

        if(count($uniqueUserIdArray) > 0) {
        	foreach ($uniqueUserIdArray as $_cid) {
        		$_customer = Mage::getModel('customer/customer')->load($_cid);
        		if($_customer->getId()) {
        			$selectResult = Mage::getModel('customerextend/cuspropmsg')->getCollection()
        							->addFieldToFilter('reciever_id', $cusId)
        							->addFieldToFilter('sender_id', $_cid)
        							->addFieldToFilter('reciever_delete', 0);
        			$selectResult->getSelect()->order('created_at DESC');
        			$selectResult->setPageSize(1);
        			$result = $selectResult->getData();
        			
        			$unreadResult = Mage::getModel('customerextend/cuspropmsg')->getCollection()
        							->addFieldToFilter('reciever_id', $cusId)
        							->addFieldToFilter('sender_id', $_cid)
        							->addFieldToFilter('reciever_delete', 0)
        							->addFieldToFilter('reciever_read', 0);
        			
	        		$customerDeatils[] = array(
	        								'cid'		=> $_customer->getId(),
	        								'name'		=> $_customer->getFirstname() . ' ' . $_customer->getLastname(),
	        								'email'		=> $_customer->getEmail(),
	        								'image'		=> Mage::helper('customerextend')->getCustomerProfileImage($_customer->getId()),
	        								'message'	=> $result[0],
	        								'unread'	=> $unreadResult->count()
	        							);
        		}
        	}
        }
        //echo '<pre>'; print_r($customerDeatils); echo '</pre>';
        return $customerDeatils;
    }
    
    public function getUserMessages($userId) {
    	$customer = Mage::getSingleton('customer/session')->getCustomer();
        $cusId = $customer->getId();
        
        $messageCollection = Mage::getModel('customerextend/cuspropmsg')->getCollection();
        $messageCollection->getSelect()->where("(`reciever_id` = '$cusId' AND `sender_id` = '$userId' AND `reciever_delete` = '0') OR (`sender_id` = '$cusId' AND `reciever_id` = '$userId' AND `sender_delete` = '0')");
        $messageCollection->getSelect()->order('created_at ASC');
		$result = $messageCollection->getData();
		
		//update read flag
		$updateResult = Mage::getModel('customerextend/cuspropmsg')->getCollection()
						->addFieldToFilter('reciever_id', $cusId)
						->addFieldToFilter('sender_id', $userId)
						->addFieldToFilter('reciever_read', 0)
						->addFieldToFilter('reciever_delete', 0);
		if($updateResult->count() > 0) {
			foreach ($updateResult as $_res) {
				$model = Mage::getModel('customerextend/cuspropmsg');		
				$model->setRecieverRead(1)
					->setId($_res->getPropMsgId());
				$model->save();
			}
		}
		
		return $result;
    }
    
    public function getUserName($cId)
    {
    	$customer = Mage::getModel('customer/customer')->load($cId);
    	return $customer->getFirstname() . ' ' . $customer->getLastname();
    }
}
