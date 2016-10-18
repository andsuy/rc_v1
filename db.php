<?php
require_once('app/Mage.php'); //Path to Magento
umask(0);
Mage::app();

$dbhost = '54.187.3.231';
$dbuser = 'amazonmagento';
$dbpass = 'trAv4WjBrHn';
$conn = mysql_connect($dbhost, $dbuser, $dbpass);
if(! $conn ) {
  die('Could not connect: ' . mysql_error());
} else {
	echo 'External Connection Established.';
}

$storeId = Mage::app()->getStore()->getStoreId();
$mailTemplate = Mage::getModel('core/email_template');
$templateId = Mage::getStoreConfig('property_section/custom_email/hostbookinginfo_template', $storeId);
//$templateId = 1;
//$templateId = 'custom_template';
$store = array(
				'frontend_name' => 'Test'
			);
$emailTemplate = $mailTemplate->loadDefault($templateId);
//$myTemplate = $mailTemplate->load($templateId);
//$myTemplate = $mailTemplate->loadByCode($templateId);

$paymentBlockHtml = 'Test Payment Block';
$order = Mage::getModel('sales/order')->load(172);
$product = Mage::getModel('catalog/product')->load(14);
$host = Mage::getModel('customer/customer')->load($product->getUserId());
$emailTemplateVariables = array(
			                'order'        => $order,
			                'billing'      => $order->getBillingAddress(),
			                'payment_html' => $paymentBlockHtml,
			                'host_name'	   => $host->getFirstname()
			            );


$processedTemplate = $emailTemplate->getProcessedTemplate($emailTemplateVariables);

//echo $processedTemplate;
$senderName = Mage::getStoreConfig('trans_email/ident_sales/name', $storeId);
$senderEmail = Mage::getStoreConfig('trans_email/ident_sales/email', $storeId);
$receiverName = $host->getName();
$receiverEmail = $host->getEmail();
$mailSubject = "New Booking # " . $order->getIncrementId();
$mailBody = $processedTemplate;
$status = 0;
$createdAt = now();
$updateAt = now();

$sql = "INSERT INTO mails_data ".
       "(sender_name, sender_email, receiver_name, receiver_email, mail_subject, mail_body, status, created_at, update_at) ".
       "VALUES ".
       "('$senderName','$senderEmail','$receiverName','$receiverEmail','$mailSubject','$mailBody','$status','$createdAt','$updateAt')";
//echo $sql;
mysql_select_db('amazonmagento');
$retval = mysql_query( $sql, $conn );
if(! $retval ) {
	die('Could not insert data: ' . mysql_error());
} else {
}
mysql_close($conn);