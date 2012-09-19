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
define("PAYFAST_MERCHANT_ID", @$this->attributeConfig->merchant_id);
define("PAYFAST_MERCHANT_KEY", @$this->attributeConfig->merchant_key);
define("PAYFAST_SANDBOX", @$this->attributeConfig->sandbox);

// General defines
define('USER_AGENT', 'Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)'); // User Agent for cURL
// Messages
// Error
define('PF_ERR_AMOUNT_MISMATCH', 'Amount mismatch');
define('PF_ERR_BAD_SOURCE_IP', 'Bad source IP address');
define('PF_ERR_CONNECT_FAILED', 'Failed to connect to PayFast');
define('PF_ERR_BAD_ACCESS', 'Bad access of page');
define('PF_ERR_INVALID_SIGNATURE', 'Security signature mismatch');
define('PF_ERR_CURL_ERROR', 'An error occurred executing cURL');
define('PF_ERR_INVALID_DATA', 'The data received is invalid');
define('PF_ERR_UKNOWN', 'Unkown error occurred');
// General
define('PF_MSG_OK', 'Payment was successful');
define('PF_MSG_FAILED', 'Payment has failed');

header('HTTP/1.0 200 OK');
flush();

$pfError = false;
$pfErrMsg = '';
$filename = 'notify.txt'; // DEBUG
$output = ''; // DEBUG
$pfParamString = '';
$pfHost = (PAYFAST_SANDBOX == 'false') ? 'www.payfast.co.za/eng/process' : 'sandbox.payfast.co.za/eng/process';

$sNotifyUrl = JURI::root() . 'index.php?option=com_enmasse&controller=payment&task=notifyUrl&payClass=payfast';
$sReturnUrl = JURI::root() . 'index.php?option=com_enmasse&controller=payment&task=gateway&orderId=' . $this->orderId . '&s=' . base64_encode('1');
$sCancelUrl = JURI::root() . 'index.php?option=com_enmasse&controller=payment&task=gateway&orderId=' . $this->orderId . '&c=' . base64_encode('1');
$dealTodayUrl = JURI::root() . 'index.php?option=com_enmasse&view=dealtoday';
if(base64_decode(JRequest::getVar('s')) == 1){
    JFactory::getApplication()->redirect($dealTodayUrl, JText::_( "Thank you for purchasing!"));
} elseif (base64_decode(JRequest::getVar('c')) == 1) {
    JFactory::getApplication()->redirect($dealTodayUrl, JText::_( "Your payment was cancelled."));
}

$user = &JFactory::getUser();
$site = &JFactory::getDocument();
$site->setTitle(JText::_('CHECK_OUT_BUTTON'));
$cart = $this->cart;
foreach ($cart->getAll() as $cartItem) {
    $item = $cartItem;
}

if (isset($_POST['x_process'])) {

    if (!$pfError) {
        $output = "Posted Variables:\n\n"; // DEBUG

        // Strip any slashes in data
        foreach ($_POST as $key => $val)
            $_POST[$key] = stripslashes($val);

        foreach ($_POST as $key => $val) {
            $output .= $key . '=' . $val . "\r\n"; // DEBUG

            if ($key != 'signature')
                $pfParamString .= $key . '=' . urlencode($val) . '&';
        }
        
        $output .= "\n\n"; // DEBUG

        // Remove the last '&' from the parameter string
        $pfParamString = substr($pfParamString, 0, -1);
        $signature = md5($pfParamString);

        $result = ($_POST['signature'] == $signature);

        $output .= "Security Signature:\n\n"; // DEBUG
        $output .= "- posted     = " . $_POST['signature'] . "\n"; // DEBUG
        $output .= "- calculated = " . $signature . "\n"; // DEBUG
        $output .= "- result     = " . ($result ? 'SUCCESS' : 'FAILURE') . "\n"; // DEBUG
    }

    if (!$pfError) {
        $validHosts = array(
            'www.payfast.co.za',
            'sandbox.payfast.co.za',
            'w1w.payfast.co.za',
            'w2w.payfast.co.za',
            );

        $validIps = array();

        foreach ($validHosts as $pfHostname) {
            $ips = gethostbynamel($pfHostname);

            if ($ips !== false)
                $validIps = array_merge($validIps, $ips);
        }

        // Remove duplicates
        $validIps = array_unique($validIps);

        if (!in_array($_SERVER['REMOTE_ADDR'], $validIps)) {
            $pfError = true;
            $pfErrMsg = PF_ERR_BAD_SOURCE_IP;
        }
    }

    if (!$pfError) {
        // Use cURL (If it's available)
        if (function_exists('curl_init')) {
            // Create default cURL object
            $ch = curl_init();

            // Base settings
            $curlOpts = array(
                // Base options
                CURLOPT_USERAGENT => USER_AGENT, // Set user agent
                CURLOPT_RETURNTRANSFER => true, // Return output as string rather than outputting it
                CURLOPT_HEADER => false, // Don't include header in output
                CURLOPT_SSL_VERIFYHOST => true,
                CURLOPT_SSL_VERIFYPEER => false,
                // Standard settings
                CURLOPT_URL => 'https://' . $pfHost . '/eng/query/validate',
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $pfParamString,
                );
            curl_setopt_array($ch, $curlOpts);

            // Execute CURL
            $res = curl_exec($ch);
            curl_close($ch);

            if ($res === false) {
                $pfError = true;
                $pfErrMsg = PF_ERR_CURL_ERROR;
            }
        }
        // Use fsockopen
        else {
            $output .= "\n\nUsing fsockopen\n\n"; // DEBUG

            // Construct Header
            $header = "POST /eng/query/validate HTTP/1.0\r\n";
            $header .= "Host: " . $pfHost . "\r\n";
            $header .= "Content-Type: application/x-www-form-urlencoded\r\n";
            $header .= "Content-Length: " . strlen($pfParamString) . "\r\n\r\n";

            // Connect to server
            $socket = fsockopen('ssl://' . $pfHost, 443, $errno, $errstr, 10);

            // Send command to server
            fputs($socket, $header . $pfParamString);

            // Read the response from the server
            $res = '';
            $headerDone = false;

            while (!feof($socket)) {
                $line = fgets($socket, 1024);

                // Check if we are finished reading the header yet
                if (strcmp($line, "\r\n") == 0) {
                    // read the header
                    $headerDone = true;
                }
                // If header has been processed
                else
                    if ($headerDone) {
                        // Read the main response
                        $res .= $line;
                    }
            }
        }
    }

    if (!$pfError) {
        // Parse the returned data
        $lines = explode("\n", $res);

        $output .= "\n\nValidate response from server:\n\n"; // DEBUG

        foreach ($lines as $line) // DEBUG

            $output .= $line . "\n"; // DEBUG
    }

    if (!$pfError) {
        // Get the response from PayFast (VALID or INVALID)
        $result = trim($lines[0]);

        $output .= "\nResult = " . $result; // DEBUG

        // If the transaction was valid
        if (strcmp($result, 'VALID') == 0) {
            // Process as required
        }
        // If the transaction was NOT valid
        else {
            // Log for investigation
            $pfError = true;
            $pfErrMsg = PF_ERR_INVALID_DATA;
        }
    }

    if ($pfError) {
        $output .= "\nError = " . $pfErrMsg;
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
		if (validateEmail(form.email_address.value)==false)
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
<form method="post" name="checkoutForm" action="https://<?php echo $pfHost; ?>" onsubmit="return validateForm()">
<table>
<tr><td>First Name: </td><td><input type="text" size="25" name="name_first" value="" /></td></tr>
<tr><td>Last Name: </td><td><input type="text" size="25" name="name_last" value="" /></td></tr>
<tr><td>Email: </td><td><input type="text" size="25" name="email_address" value="<?php echo $user->email; ?>" /></td></tr>
<tr><td><input name="x_process" type="submit" value="Process" /></td><td>
<input type="hidden" type="text" name="pf_payment_id" value="<?php echo $this->orderId; ?>"/>
<input type="hidden" type="text" name="item_name" value="<?php echo $item->item->name; ?>"/>
<input type="hidden" type="text" name="item_description" value="<?php echo $item->item->short_desc; ?>"/>
<input type="hidden" type="text" name="amount" value="<?php echo ($price * $cartItem->getCount()); ?>" />
<input type="hidden" type="text" name="merchant_id" value="<?php echo $this->attributeConfig->merchant_id; ?>" />
<input type="hidden" type="text" name="merchant_key" value="<?php echo $this->attributeConfig->merchant_key; ?>" />
<input type="hidden" type="text" name="return_url" value="<?php echo $sReturnUrl; ?>" />
<input type="hidden" type="text" name="cancel_url" value="<?php echo $sCancelUrl; ?>" />
<input type="hidden" type="text" name="notify_url" value="<?php echo $sNotifyUrl; ?>" />
</td></tr>
</table>
</form>