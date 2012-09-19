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

$point = $this->cart->getPoint();
$arrItems = $this->cart->getAll();
$cartItem = array_pop($arrItems);
$price = number_format($cartItem->item->price * $cartItem->item->prepay_percent / 100, 2);
?>
<div style="margin-top:0px">
<form id="paymentForm" name="paymentForm" method="POST"
    action="https://sandbox.google.com/checkout/api/checkout/v2/checkoutForm/Merchant/<?php echo $this->attributeConfig->MerchantID; ?>">
	<input type="hidden" name="item_name_1" value="<?=$cartItem->item->name?>"/>
	<input type="hidden" name="item_description_1" value="<?=$cartItem->item->short_desc?>"/>
	<input type="hidden" name="item_price_1" value="<?=$price?>"/>
	<input type="hidden" name="item_currency_1" value="<?php echo $this->attributeConfig->currency; ?>"/>
	<input type="hidden" name="item_quantity_1" value="<?=$cartItem->count?>"/>
	<input type="hidden" name="item_merchant_id_1" value="<?=$cartItem->item->deal_code?>"/>

<input type="hidden"
  name="checkout-flow-support.merchant-checkout-flow-support.shipping-methods.flat-rate-shipping-1.name"
  value="UPS Next Day Air"/>
<input type="hidden"
  name="checkout-flow-support.merchant-checkout-flow-support.shipping-methods.flat-rate-shipping-1.price"
  value="20.00"/>
<input type="hidden"
  name="checkout-flow-support.merchant-checkout-flow-support.shipping-methods.flat-rate-shipping-1.price.currency"
  value="USD"/>

<input type="hidden"
  name="checkout-flow-support.merchant-checkout-flow-support.shipping-methods.flat-rate-shipping-2.name"
  value="UPS Ground"/>
<input type="hidden"
  name="checkout-flow-support.merchant-checkout-flow-support.shipping-methods.flat-rate-shipping-2.price"
  value="15.00"/>
<input type="hidden"
  name="checkout-flow-support.merchant-checkout-flow-support.shipping-methods.flat-rate-shipping-2.price.currency"
  value="USD"/>

<input type="hidden" 
  name="checkout-flow-support.merchant-checkout-flow-support.edit-cart-url" 
  value="<?php echo JURI::base().JRoute::_('index.php?option=com_enmasse&view=dealtoday&Itemid=101', false)?>"/>
<input type="hidden" 
  name="checkout-flow-support.merchant-checkout-flow-support.continue-shopping-url" 
  value="<?php echo JURI::base().JRoute::_('index.php?option=com_enmasse&view=dealtoday&Itemid=101', false)?> "/> 
</form> 
<?php echo JText::_('PAYPAL_REDIRECT'); ?>
</div>

<script>
	jQuery(document).ready(function(){
		document.paymentForm.submit();
	})
	//#setTimeout(function(){document.paymentForm.submit()}, 3000);    
</script>
<?php 
	$this->cart->deleteAll();
	JFactory::getSession()->set('cart', null);
?>