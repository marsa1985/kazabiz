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
define("AUTHORIZENET_API_LOGIN_ID", $this->attributeConfig->api_login_id);
define("AUTHORIZENET_TRANSACTION_KEY", $this->attributeConfig->transaction_key);
define("AUTHORIZENET_SANDBOX", $this->attributeConfig->sandbox);
define("TEST_REQUEST", $this->attributeConfig->test_request);	
require_once 'sdk/AuthorizeNet.php';
$pageURL = 'http';
if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
	$pageURL .= "://";
if ($_SERVER["SERVER_PORT"] != "80")
{
	$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"];
}
else
{
	$pageURL .= $_SERVER["SERVER_NAME"];
}
$user  =& JFactory::getUser();
$site =& JFactory::getDocument();
$site->setTitle(JText::_('CHECK_OUT_BUTTON')); 
$cart = $this->cart;
foreach($cart->getAll() as $cartItem) {
	$item = $cartItem; 
}
if(isset($_POST['x_process']))
{
	$transaction = new AuthorizeNetAIM;
    $transaction->setSandbox(AUTHORIZENET_SANDBOX);
    $transaction->setFields(
        array(
        'amount' => $_POST['x_amount'], 
        'card_num' => $_POST['x_card_num'], 
        'exp_date' => $_POST['x_exp_month'] . "/" . $_POST['x_exp_year'],
        'first_name' => $_POST['x_first_name'],
        'last_name' => $_POST['x_last_name'],
        'email' => $_POST['x_email'],
        'card_code' => $_POST['x_card_code'],
        'invoice_num' => $_POST['x_invoice_num'],
        'description' => $_POST['x_description'],
		'type' => $_POST['x_type'],
        )
    );
	if($_POST['x_type'] == "AUTH_ONLY")
	{
		$response = $transaction->authorizeOnly();
	}
	elseif($_POST['x_type'] == "AUTH_CAPTURE")
	{
    	$response = $transaction->authorizeAndCapture();	
	}
	else
	{
		echo "<span style=\"color: red;\">There is an error when making the transaction, please contact Administrator!</span><br/>";
	}

    if ($response->approved)
    {
		JFactory::getSession()->set('cart', null);
?>	
		<form name="paymentForm" method="post" action="<?php echo JURI::base() . "index.php?option=com_enmasse&controller=payment&task=notifyUrl&payClass=authorizenet"; ?>" id="checkout_form">
		<input type="hidden" type="text" name="approved" value="true"/>
		<input type="hidden" type="text" name="authorization_code" value="<?php echo $response->authorization_code; ?>"/>
		<input type="hidden" type="text" name="transaction_id" value="<?php echo $response->transaction_id; ?>">
		<input type="hidden" type="text" name="invoice_number" value="<?php echo $response->invoice_number;; ?>">
		<input type="hidden" type="text" name="amount" value="<?php echo $response->amount; ?>">
		<input type="hidden" type="text" name="account_number" value="<?php echo $response->account_number; ?>">
		</form>	
	<script>
		document.paymentForm.submit();
	</script>		
<?php
    }
    else
    {
    	echo "<span style=\"color: red;\">".$response->response_reason_text."</span><br/>";
    }
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
		if (form.x_card_num.value == "" || isNaN(form.x_card_num.value))
		{
			alert("Please enter card number!");
			return false;
		}
		if (form.x_card_code.value == "" || isNaN(form.x_card_code.value))
		{
			alert("Please enter card code!");
			return false;
		}
		if (form.x_first_name.value == "")
		{
			alert("Please enter first name!");
			return false;
		}
		if (form.x_last_name.value == "")
		{
			alert("Please enter last name!");
			return false;
		}
		if (validateEmail(form.x_email.value)==false)
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
<form method="post" name="checkoutForm" action="<?php echo $pageURL . $_SERVER["REQUEST_URI"]; ?>" onsubmit="return validateForm()">
<table>
<tr><td>Credit Card Number (without spaces): </td><td><input type="text" size="25" name="x_card_num" value=""></input></td></tr>
<tr><td>Expired Date (MM/YY): </td><td><select name="x_exp_month">
			<option value="01">01</option>
			<option value="02">02</option>
			<option value="03">03</option>
			<option value="04">04</option>
			<option value="05">05</option>
			<option value="06">06</option>
			<option value="07">07</option>
			<option value="08">08</option>
			<option value="09">09</option>
			<option value="10">10</option>
			<option value="11">11</option>
			<option value="12">12</option>
		</select> / 
	<select name="x_exp_year">
	<?php
		$current_year = date("Y");
		for ($i=$current_year; $i<=($current_year+20); $i++)
		{
			echo "<option value=\"$i\">$i</option>";
		}
	?>
</select></td></tr>
<tr><td>CCV: </td><td><input type="text" size="4" name="x_card_code" value=""></input></td></tr>
<tr><td>First Name: </td><td><input type="text" size="25" name="x_first_name" value=""></input></td></tr>
<tr><td>Last Name: </td><td><input type="text" size="25" name="x_last_name" value=""></input></td></tr>
<tr><td>Email: </td><td><input type="text" size="25" name="x_email" value="<?php echo $user->email; ?>"></input></td></tr>
<tr><td><input name="x_process" type="submit" value="Process" ></td><td>
<input type="hidden" type="text" name="x_invoice_num" value="<?php echo $this->orderId; ?>"/>
<input type="hidden" type="text" name="x_description" value="<?php echo $item->item->short_desc; ?>"/>
<input type="hidden" type="text" name="x_amount" value="<?php echo ($price * $cartItem->getCount()); ?>">
<input type="hidden" type="text" name="x_type" value="<?php echo $this->attributeConfig->type; ?>">

</td></tr>
</table>
</form>