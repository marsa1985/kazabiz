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
define("WORLDPAY_TRANS_ID", @$this->attributeConfig->transId);
define("WORLDPAY_CURRENCY", @$this->attributeConfig->currency);
define("WORLDPAY_TESTMODE", @$this->attributeConfig->testMode);

$wpHost = "https://secure.wp3.rbsworldpay.com/wcc/purchase";

$user = &JFactory::getUser();
$site = &JFactory::getDocument();
$site->setTitle(JText::_('CHECK_OUT_BUTTON'));
$cart = $this->cart;
foreach ($cart->getAll() as $cartItem) {
    $item = $cartItem;
}

if (isset($_POST['x_process'])) {
    
}

?>
<script language="javascript" type="text/javascript">
	function validateEmail($email)
	{
	    var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
	    if( !emailReg.test( $email ) )
	    {
	    	return false;
	    }
	    else
		{
	    	return true;
	    }
	}

	function validateForm()
	{
		var form = document.checkoutForm;
		if (form.name_first.value == "")
		{
			alert("Please enter first name!");
			return false;
		}
		if (form.name_last.value == "")
		{
			alert("Please enter last name!");
			return false;
		}
		if (validateEmail(form.email.value)==false)
		{
			alert("Please enter a valid email!");
			return false;
		}			
		return true;
	}		
</script>
<style>
table, table th, table td {
	border: 0 !important;
}
.maincol_full {
	padding: 10px;
}
</style>
<?php
$cartItem = array_pop($this->cart->getAll());
$price = number_format($cartItem->item->price * $cartItem->item->prepay_percent / 100, 2);
?>
<h1>Please enter your information:</h1>
<form method="post" name="checkoutForm" action="<?php echo $wpHost; ?>" onsubmit="return validateForm()">
<table>
<tr><td>First Name: </td><td><input type="text" size="25" name="name_first" value="" /></td></tr>
<tr><td>Last Name: </td><td><input type="text" size="25" name="name_last" value="" /></td></tr>
<tr><td>Email: </td><td><input type="text" size="25" name="email" value="<?php echo $user->email; ?>" /></td></tr>
<tr><td><input name="x_process" type="submit" value="Process" /></td><td>
<input type="hidden" type="text" name="desc" value="<?php echo $item->item->name; ?>"/>
<input type="hidden" type="text" name="cartId" value="<?php echo $this->orderId; ?>"/>
<input type="hidden" name="testMode" value="<?php echo $this->attributeConfig->testMode; ?>" />
<input type="hidden" type="text" name="currency" value="<?php echo $this->attributeConfig->currency; ?>"/>
<input type="hidden" type="text" name="amount" value="<?php echo ($price * $cartItem->getCount()); ?>" />
<input type="hidden" type="text" name="transId" value="<?php echo $this->attributeConfig->transId; ?>" />
<?php if($this->attributeConfig->testMode != 0) { ?>
    <input type="hidden" name="name" value="AUTHORISED" />
<?php } ?>
</td></tr>
</table>
</form>