<?php
/*------------------------------------------------------------------------
# En Masse - Social Buying Extension 2010
# ------------------------------------------------------------------------
# By Matamko.com
# Copyright (C) 2010 Matamko.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.matamko.com
# Technical Support:  Visit our forum at www.matamko.com
-------------------------------------------------------------------------*/

// load language pack
$language = JFactory::getLanguage();
$base_dir = JPATH_SITE.DS.'components'.DS.'com_enmasse';
$version = new JVersion;
$joomla = $version->getShortVersion();
if(substr($joomla,0,3) >= '1.6'){
    $extension = 'com_enmasse16';
}else{
    $extension = 'com_enmasse';
}
if($language->load($extension, $base_dir, $language->getTag(), true) == false)
{
	 $language->load($extension, $base_dir, 'en-GB', true);
}

$site = JFactory::getDocument();
$site->setTitle(JText::_('CHECK_OUT_BUTTON')); 
$cartItem = array_pop($this->cart->getAll());

$price = number_format($cartItem->item->price * $cartItem->item->prepay_percent / 100, 2);
// remove the decimal point by multiply the price by 100
$price = $price * 100;
// multiply price with total item
$price = $price * $cartItem->getCount();
// currencty to be default as GBP if it's not set in payment configuration
$currency = "GBP";
if ( isset($this->attributeConfig->CURRENCY) && trim($this->attributeConfig->CURRENCY) != "")
{
	$currency = $this->attributeConfig->CURRENCY;
}
// account to be "internet" if it's not set in payment configuration
$account = "internet";
if ( isset($this->attributeConfig->ACCOUNT) && trim($this->attributeConfig->ACCOUNT) != "")
{
	$account = $this->attributeConfig->ACCOUNT;
}

$merchantId = $this->attributeConfig->MERCHANT_ID;
$secret = $this->attributeConfig->SECRET;
$orderId = $this->orderId;

// time stamp
$timestamp = DatetimeWrapper::getDateOfNow('YmdHis');

// hash value
$hash = sha1($timestamp.".".$merchantId.".".$orderId.".".$price.".".$currency);
$doubleHash = sha1($hash.".".$secret);
// store the hash into session
$session = & JFactory::getSession();
$session->set("realExHash", $doubleHash);

// not used
$returnUrl = JURI::root() . 'index.php?option=com_enmasse&controller=payment&task=notifyUrl&payClass=realex';
?>

<form name="paymentForm" action="https://epage.payandshop.com/epage.cgi" method="post">

	<input type="hidden" name="MERCHANT_ID" value="<?php echo $merchantId; ?>">
	<input type="hidden" name="ACCOUNT" value="<?php echo $account; ?>">
	<input type="hidden" name="ORDER_ID" value="<?php echo $orderId; ?>">
	<input type="hidden" name="AMOUNT" value="<?php echo $price; ?>">
	<input type="hidden" name="CURRENCY" value="<?php echo $currency; ?>">
	<input type="hidden" name="TIMESTAMP" value="<?php echo $timestamp; ?>">
	<input type="hidden" name="AUTO_SETTLE_FLAG" value="1">
	<input type="hidden" name="SHA1HASH" value="<?php echo $doubleHash;?>">

</form>
<?php echo JText::_('REALEX_REDIRECT'); ?>
<script>
    document.paymentForm.submit();
</script>