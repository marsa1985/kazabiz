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
$arCartItem = array_pop($this->cart->getAll());
$flPrice = number_format($arCartItem->item->price * $arCartItem->item->prepay_percent / 100, 2);
$sDealName = $arCartItem->item->name;
$sNotifyUrl = JURI::root() . 'index.php?option=com_enmasse&controller=payment&task=notifyUrl&payClass=moneybookers';
$sReturnUrl = JURI::root() . 'index.php?option=com_enmasse&view=dealtoday';
$sCancelUrl = JURI::root() . 'index.php?option=com_enmasse&view=dealtoday';
?>
<form name="paymentForm" action="https://www.moneybookers.com/app/payment.pl" method="post" target="_self"> 
  <input type="hidden" name="pay_to_email" value="<?php echo $this->attributeConfig->merchant_email; ?>"> 
  <input type="hidden" name="status_url" value="<?php echo $sNotifyUrl; ?>"> 
 <input type="hidden" name="return_url" value="<?php echo $sReturnUrl; ?>"> 
  <input type="hidden" name="cancel_url" value="<?php echo $sCancelUrl; ?>"> 
 <input type="hidden" name="language" value="<?php  echo $this->attributeConfig->language_code; ?>">  
  <input type="hidden" name="language" value="<?php  echo $this->attributeConfig->country_code; ?>"> 
  <input type="hidden" name="amount" value="<?php echo $flPrice; ?>"> 
  <input type="hidden" name="currency" value="<?php echo $this->attributeConfig->currency_code; ?>"> 
  <input type="hidden" name="detail1_description" value="<?php echo $sDealName ;?>"> 
  <input type="hidden" name="detail1_text" value="Make Purchase from <?php echo $this->systemName ;?>"> 
  <input type="hidden" name="confirmation_note" value="Thank your for your payment!"> 
  <input type="hidden" name="transaction_id" value="<?php echo $this->orderDisplayId; ?>">  
</form> 

<div style="margin-top:0px"><?php echo JText::_('PAYPAL_REDIRECT'); ?></div>
<script>
    document.paymentForm.submit();
</script>
<?php 
	$this->cart->deleteAll();
	JFactory::getSession()->set('cart', serialize($cart));
?>