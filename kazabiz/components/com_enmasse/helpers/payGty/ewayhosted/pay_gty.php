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

//Turn off error reporting
error_reporting (E_ALL ^ E_NOTICE);

//define default values for eway
define('EWAY_DEFAULT_CUSTOMER_ID','');
define('EWAY_DEFAULT_PAYMENT_METHOD', 'REAL_TIME'); // possible values are: REAL_TIME, REAL_TIME_CVN, GEO_IP_ANTI_FRAUD
define('EWAY_DEFAULT_LIVE_GATEWAY', false); //<false> sets to testing mode, <true> to live mode

//define script constants
define('REAL_TIME', 'REAL-TIME');
define('REAL_TIME_CVN', 'REAL-TIME-CVN');
define('GEO_IP_ANTI_FRAUD', 'GEO-IP-ANTI-FRAUD');

//define URLs for payment gateway
define('EWAY_PAYMENT_LIVE_REAL_TIME', 'https://www.eway.com.au/gateway/xmlpayment.asp');
define('EWAY_PAYMENT_LIVE_REAL_TIME_TESTING_MODE', 'https://www.eway.com.au/gateway/xmltest/testpage.asp');
define('EWAY_PAYMENT_LIVE_REAL_TIME_CVN', 'https://www.eway.com.au/gateway_cvn/xmlpayment.asp');
define('EWAY_PAYMENT_LIVE_REAL_TIME_CVN_TESTING_MODE', 'https://www.eway.com.au/gateway_cvn/xmltest/testpage.asp');
define('EWAY_PAYMENT_LIVE_GEO_IP_ANTI_FRAUD', 'https://www.eway.com.au/gateway_beagle/xmlbeagle.asp');
define('EWAY_PAYMENT_LIVE_GEO_IP_ANTI_FRAUD_TESTING_MODE', 'https://www.eway.com.au/gateway_beagle/test/xmlbeagle_test.asp'); //in testing mode process with REAL-TIME
define('EWAY_PAYMENT_HOSTED_REAL_TIME', 'https://www.eway.com.au/gateway/payment.asp');
define('EWAY_PAYMENT_HOSTED_REAL_TIME_TESTING_MODE', 'https://www.eway.com.au/gateway/payment.asp');
define('EWAY_PAYMENT_HOSTED_REAL_TIME_CVN', 'https://www.eway.com.au/gateway_cvn/payment.asp');
define('EWAY_PAYMENT_HOSTED_REAL_TIME_CVN_TESTING_MODE', 'https://www.eway.com.au/gateway_cvn/payment.asp');
	
class EwayPaymentLive {
    var $myGatewayURL;
    var $myCustomerID;
    var $myTransactionData = array();
    var $myCurlPreferences = array();

    //Class Constructor
	function EwayPaymentLive($customerID = EWAY_DEFAULT_CUSTOMER_ID, $method = EWAY_DEFAULT_PAYMENT_METHOD ,$liveGateway  = EWAY_DEFAULT_LIVE_GATEWAY) {
		$this->myCustomerID = $customerID;
	    switch($method){

		    case 'REAL_TIME';

		    		if($liveGateway)
		    			$this->myGatewayURL = EWAY_PAYMENT_LIVE_REAL_TIME;
		    		else
	    				$this->myGatewayURL = EWAY_PAYMENT_LIVE_REAL_TIME_TESTING_MODE;
	    		break;
	    	 case 'REAL_TIME_CVN';
		    		if($liveGateway)
		    			$this->myGatewayURL = EWAY_PAYMENT_LIVE_REAL_TIME_CVN;
		    		else
	    				$this->myGatewayURL = EWAY_PAYMENT_LIVE_REAL_TIME_CVN_TESTING_MODE;
	    		break;
	    	case 'GEO_IP_ANTI_FRAUD';
		    		if($liveGateway)
		    			$this->myGatewayURL = EWAY_PAYMENT_LIVE_GEO_IP_ANTI_FRAUD;
		    		else
		    			//in testing mode process with REAL-TIME
	    				$this->myGatewayURL = EWAY_PAYMENT_LIVE_GEO_IP_ANTI_FRAUD_TESTING_MODE;
	    		break;
    	}
	}
	
	
	//Payment Function
	function doPayment() {
		$xmlRequest = "<ewaygateway><ewayCustomerID>" . $this->myCustomerID . "</ewayCustomerID>";
		foreach($this->myTransactionData as $key=>$value)
			$xmlRequest .= "<$key>$value</$key>";
        $xmlRequest .= "</ewaygateway>";
		$xmlResponse = $this->sendTransactionToEway($xmlRequest);
		if($xmlResponse!=""){
			$responseFields = $this->parseResponse($xmlResponse);
			return $responseFields;
		}
		else
		{
			//die("Error in XML response from eWAY: " + $xmlResponse);
			die("Error in XML response from eWAY");
		}
	}

	//Send XML Transaction Data and receive XML response
	function sendTransactionToEway($xmlRequest) {
		$ch = curl_init($this->myGatewayURL);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlRequest);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        foreach($this->myCurlPreferences as $key=>$value)
        	curl_setopt($ch, $key, $value);

        $xmlResponse = curl_exec($ch);

        if(curl_errno( $ch ) == CURLE_OK)
        	return $xmlResponse;
	}
	
	
	//Parse XML response from eway and place them into an array
	function parseResponse($xmlResponse)
	{
		$xml_parser = xml_parser_create();
		xml_parse_into_struct($xml_parser, $xmlResponse, $xmlData, $index);
        $responseFields = array();
        foreach($xmlData as $data)
        {
	    	if($data["level"] == 2)
	    	{
        		$responseFields[$data["tag"]] = $data["value"];
	    	}
        }       		
        return $responseFields;
	}

	
	//Set Transaction Data
	//Possible fields: "TotalAmount", "CustomerFirstName", "CustomerLastName", "CustomerEmail", "CustomerAddress", "CustomerPostcode", "CustomerInvoiceDescription", "CustomerInvoiceRef",
	//"CardHoldersName", "CardNumber", "CardExpiryMonth", "CardExpiryYear", "TrxnNumber", "Option1", "Option2", "Option3", "CVN", "CustomerIPAddress", "CustomerBillingCountry"
	function setTransactionData($field, $value) {
		//if($field=="TotalAmount")
		//	$value = round($value*100);
		$this->myTransactionData[$field] = htmlentities(trim($value));
	}
	
	
	//receive special preferences for Curl
	function setCurlPreferences($field, $value) {
		$this->myCurlPreferences[$field] = $value;
	}
		
	
	//obtain visitor IP even if is under a proxy
	function getVisitorIP(){
		$ip = $_SERVER["REMOTE_ADDR"];
		$proxy = $_SERVER["HTTP_X_FORWARDED_FOR"];
		if(ereg("^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$",$proxy))
		        $ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
		return $ip;
	}
}






//////////////////////////////////////////////////////////////////////////////////////////////





if (isset($_POST['btnProcess']))
{
	$ewayCustomerID = $_POST['ewayCustomerID'];
	$ewayTotalAmount = $_POST['ewayTotalAmount'];
	$ewayCustomerFirstName = $_POST['ewayCustomerFirstName'];
	$ewayCustomerLastName = $_POST['ewayCustomerLastName'];
	$ewayCustomerEmail = $_POST['ewayCustomerEmail'];
	$ewayCustomerAddress = $_POST['ewayCustomerAddress'];
	$ewayCustomerPostcode = $_POST['ewayCustomerPostcode'];
	$ewayCustomerInvoiceDescription = $_POST['ewayCustomerInvoiceDescription'];
	$ewayCustomerInvoiceRef = $_POST['ewayCustomerInvoiceRef'];
	$ewayCardHoldersName = $_POST['ewayCardHoldersName'];
	$ewayCardNumber = $_POST['ewayCardNumber'];
	$ewayCardExpiryMonth = $_POST['ewayCardExpiryMonth'];
	$ewayCardExpiryYear = $_POST['ewayCardExpiryYear'];
	$ewayCVN = $_POST['ewayCVN'];	
	$ewayTrxnNumber = $_POST['ewayTrxnNumber'];
	$ewayOption1 = $_POST['ewayOption1'];
	$ewayOption2 = $_POST['ewayOption2'];
	$ewayOption3 = $_POST['ewayOption3'];

	// Set the payment details
	//$eway = new EwayPaymentLive($eWAY_CustomerID, $eWAY_PaymentMethod, $eWAY_UseLive);
	$eway = new EwayPaymentLive($ewayCustomerID, EWAY_DEFAULT_PAYMENT_METHOD, EWAY_DEFAULT_LIVE_GATEWAY);

	$eway->setTransactionData("ewayCustomerID", $ewayCustomerID); //mandatory field
	$eway->setTransactionData("ewayTotalAmount", $ewayTotalAmount); //mandatory field
	$eway->setTransactionData("ewayCustomerFirstName", $ewayCustomerFirstName);
	$eway->setTransactionData("ewayCustomerLastName", $ewayCustomerLastName);
	$eway->setTransactionData("ewayCustomerEmail", $ewayCustomerEmail);
	$eway->setTransactionData("ewayCustomerAddress", $ewayCustomerAddress);
	$eway->setTransactionData("ewayCustomerPostcode", $ewayCustomerPostcode);
	$eway->setTransactionData("ewayCustomerInvoiceDescription", $ewayCustomerInvoiceDescription);
	$eway->setTransactionData("ewayCustomerInvoiceRef", $ewayCustomerInvoiceRef);
	$eway->setTransactionData("ewayCardHoldersName", $ewayCardHoldersName); //mandatory field
	$eway->setTransactionData("ewayCardNumber", $ewayCardNumber); //mandatory field
	$eway->setTransactionData("ewayCardExpiryMonth", $ewayCardExpiryMonth); //mandatory field
	$eway->setTransactionData("ewayCardExpiryYear", $ewayCardExpiryYear); //mandatory field
	$eway->setTransactionData("ewayTrxnNumber", $ewayTrxnNumber);
	$eway->setTransactionData("ewayCVN", $ewayCVN);
	$eway->setTransactionData("ewayOption1", $ewayOption1);
	$eway->setTransactionData("ewayOption2", $ewayOption2);
	$eway->setTransactionData("ewayOption3", $ewayOption3);	
	$eway->setCurlPreferences(CURLOPT_SSL_VERIFYPEER, 0); // Require for Windows hosting
	
	$ewayResponseFields = $eway->doPayment();
	
	if(strtolower($ewayResponseFields["EWAYTRXNSTATUS"])=="false")
	{
		
		print "Transaction Error: " . $ewayResponseFields["EWAYTRXNERROR"] . "<br>\n";
		
		/*
		foreach($ewayResponseFields as $key => $value)
		{
			print "\n<br>\$ewayResponseFields[\"$key\"] = $value";
		}
		*/
		
	}
	else if(strtolower($ewayResponseFields["EWAYTRXNSTATUS"])=="true")
	{
		// payment succesfully sent to gateway
		// Payment succeeded get values returned
		
		$lblResult = " Result: " . $ewayResponseFields["EWAYTRXNSTATUS"] . "<br>";
		$lblResult .= " AuthCode: " . $ewayResponseFields["EWAYAUTHCODE"] . "<br>";
		$lblResult .= " Error: " . $ewayResponseFields["EWAYTRXNERROR"] . "<br>";
		$lblResult .= " eWAYInvoiceRef: " . $ewayResponseFields["EWAYTRXNREFERENCE"] . "<br>";
		$lblResult .= " Amount: " . $ewayResponseFields["EWAYRETURNAMOUNT"] . "<br>";
		$lblResult .= " Txn Number: " . $ewayResponseFields["EWAYTRXNNUMBER"] . "<br>";
		$lblResult .= " Option1: " . $ewayResponseFields["EWAYTRXNOPTION1"] . "<br>";
		$lblResult .= " Option2: " . $ewayResponseFields["EWAYTRXNOPTION2"] . "<br>";
		$lblResult .= " Option3: " . $ewayResponseFields["EWAYTRXNOPTION3"] . "<br>";
 		$this->cart->deleteAll();
 		JFactory::getSession()->set('cart', serialize($cart));		
		$link = $ewayResponseFields["EWAYTRXNOPTION1"] . "&trxnauthcode=" . $ewayResponseFields["EWAYAUTHCODE"] . "&trxnstatus=" . strtolower($ewayResponseFields["EWAYTRXNSTATUS"]) . "&trxerror=" . $ewayResponseFields["EWAYTRXNERROR"] . "&trxnumber=" . $ewayResponseFields["EWAYTRXNNUMBER"] . "&returnamount=" . $ewayResponseFields["EWAYRETURNAMOUNT"];
		JFactory::getApplication()->redirect($link);
	}
	else
	{
		// invalid response recieved from server.
		$lblResult =  "Error: An invalid response was recieved from the payment gateway.";
		echo $lblResult;
	}  
}
else
{
	$user  = JFactory::getUser();
	$site = JFactory::getDocument();
	$site->setTitle("eWay transaction"); 
    $cartItem = array_pop($this->cart->getAll());
    $price = number_format($cartItem->item->price * $cartItem->item->prepay_percent / 100, 2);    
?>
<style>
    table#eway, table#eway th, table#eway td {
        border: 0 !important;
    }
    div#eway_form {
        margin: 20px;
    }
</style>
<div id="eway_form">
	<h1>Please fill out the form and process transaction</h1>
	<form id="paymentForm" method="post" action="<?php echo JURI::base(); ?>" ENCTYPE="multipart/form-data">
	<table id="eway">
	<tr><td>First name:</td><td><input name="ewayCustomerFirstName" value=""></td></tr>
	<tr><td>Last name:</td><td><input name="ewayCustomerLastName" value=""></td></tr>
	<tr><td>Email:</td><td><input name="ewayCustomerEmail" value="<?php echo $user->email; ?>"></td></tr>
	<tr><td>Address:</td><td><input name="ewayCustomerAddress" value=""></td></tr>
	<tr><td>Postcode:</td><td><input name="ewayCustomerPostcode" value=""></td></tr>
	<tr style="display:none"><td>Invoice Description:</td><td><input name="ewayCustomerInvoiceDescription" value=""></td></tr>
	<tr style="display:none"><td>Invoice Ref:</td><td><input name="ewayCustomerInvoiceRef" value=""></td></tr>
	<tr><td>Card Holders Name:</td><td><input name="ewayCardHoldersName" value=""></td></tr>
	<tr><td>Card Number:</td><td><input name="ewayCardNumber" value=""></td></tr>	
	<tr><td>eWay Card Expiry Date (Month/Year):</td><td>	 <select name="ewayCardExpiryMonth">
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
	<select name="ewayCardExpiryYear">
	<?php
		$current_year = date('y');
		for ($i=$current_year; $i<=20; $i++)
		{
			echo "<option value=\"$i\">$i</option>";
		}
	?>
		</select></td></tr>
	<!-- <tr><td>Credit card verification number:</td><td><input name="ewayCVN" maxlength="4" value="123"></td></tr> -->
	<tr style="display:none"><td>Trxn Number:</td><td><input name="ewayTrxnNumber" value=""></td></tr>	
	<tr style="display:none"><td>Option 1:</td><td><input name="ewayOption1" value="<?php echo $this->notifyUrl; ?>"></td></tr>	
	<tr style="display:none"><td>Option 2:</td><td><input name="ewayOption2" value=""></td></tr>	
	<tr style="display:none"><td>Option 3:</td><td><input name="ewayOption3" value=""></td></tr>   
	</table>
	<input type="hidden" name="ewayCustomerID" value="<?php echo $this->attributeConfig->ewayCustomerID; ?>">
	<input type="hidden" name="ewayTotalAmount" value="<?php echo ($price * $cartItem->getCount() * 100); ?>">
	<input type="hidden" name="option" value="com_enmasse"/>
	<input type="hidden" name="controller" value="payment"/>
	<input type="hidden" name="task" value="gateway"/>
    <input type="submit" name="btnProcess" value="Process Transaction" id="btnProcess" />
	</form>
</div>
<?php
}
?>
