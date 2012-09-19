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
if($this->attributeConfig->demo == "true")
{
    $sid = '1303908';
    $demo ='Y';
}	
else
{		
    $sid = $this->attributeConfig->sid;
    $demo ='N';
}
$returnUrl = JURI::root() . 'index.php?option=com_enmasse&controller=payment&task=notifyUrl&payClass=twocheckout';
?>

<form name="paymentForm" action="https://www.2checkout.com/checkout/purchase" method="post">
		<input type="hidden" name="sid" value="<?php echo $sid; ?>"/>
		<input type="hidden" name="cart_order_id" value="<?php echo $this->orderId; ?>"/>
		<input type="hidden" name="total" value="<?php echo ($price * $cartItem->getCount()); ?>"/>
		<input type="hidden" name="c_prod_1" value="<?php echo $cartItem->item->id; ?>,<?php echo $cartItem->getCount() ?>"/>
		<input type="hidden" name="c_name_1" value="<?php echo $cartItem->item->name; ?>"/>
		<input type="hidden" name="c_description_1" value="<?php echo $cartItem->item->short_desc; ?>"/>
		<input type="hidden" name="c_price_1" value="<?php echo $cartItem->item->price; ?>"/>
		<input type="hidden" name="demo" value="<?php echo $demo; ?>"/>
		<input type="hidden" name="return_url" value="<?php echo $returnUrl; ?>"/>
		<input type="hidden" name="id_type" value="1"/>
</form>
<?php echo JText::_('TWOCHECKOUT_REDIRECT'); ?>
<script>
    document.paymentForm.submit();
</script>