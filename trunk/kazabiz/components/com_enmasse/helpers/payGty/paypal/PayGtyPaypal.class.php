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

//------------------------------------------------------------------------
// Class for providing the encapsulation of the Payment Gateway

class PayGtyPaypal
{
	public static function returnStatus()
	{
		$status = new JObject();
		$status->coupon = 'Free';
		$status->order  = 'Unpaid';
		return $status;
	}
	public static function checkConfig($payGty)
	{
		$attribute_config = json_decode($payGty->attribute_config);
		if ( !isset($attribute_config->merchant_email) || trim($attribute_config->merchant_email) == "")
		{
			return false;
		}
		return true;
	}
	
	public static function makePayment($amt)
	{
		return false;
	}

	public static function validateTxn($payClass)
	{
		if($_REQUEST['payment_status']=="Refunded")
			return false;
		//$_REQUEST['payment_status']=="Completed"
			
		$payGtyTxnId 	= $_REQUEST['txn_id'];
		$amt 			= $_REQUEST['mc_gross'];
		
		//-----------------------------------------------------------------------
		// To do checking of the payment if it is really from Paypal (PDT need to be enabled for checking)
		 
		// read the post from PayPal system and add 'cmd'
		$req = 'cmd=_notify-validate';

		foreach ($_REQUEST as $key => $value)
		{
			$value = urlencode(stripslashes($value));
			$req .= "&$key=$value";
		}

		// post back to PayPal system to validate
		$header = "POST /cgi-bin/webscr HTTP/1.0\r\n";
		$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
		$header .= "Content-Length: " . strlen($req) . "\r\n\r\n";

		// If possible, securely post back to paypal using HTTPS, cannot use http, as it don't recongize
		$fp = fsockopen ('www.paypal.com', 80, $errno, $errstr, 30);

		// assign posted variables to local variables


		if(!$fp)
		{
			throw new Exception("Could not call Paypal PDT for valid the transaction : <br/>NotifyUrl: ".$notifyUrl."<br/>ErrNo:".$errno." <br/>ErrStr:" . $errstr . " <br/>POST Data Sent:". $req);
		}
		else
		{
			fputs ($fp, $header . $req);
			while (!feof($fp))
			{
				$res = fgets ($fp, 1024);
				echo "(".$res.")";
				if (strcmp ($res, "INVALID") == 0)
				{
					throw new Exception("Payment Failed! Update Failed, Payment INVALID @ ". $notifyUrl ."<br/><br/>ERROR DETAIL:".$error);
				}
			}
			fclose ($fp);
		}
		return true;
	}
	
	public static function generatePaymentDetail()
	{
		$paymentDta = array();
		
		$paymentDta["txn_id"]            = $_REQUEST['txn_id'];
		$paymentDta["payment_status"]    = $_REQUEST['payment_status'];
		$paymentDta["mc_currency"]       = $_REQUEST['mc_currency'];
		$paymentDta["mc_gross"]          = $_REQUEST['mc_gross'];
		
		return $paymentDta;
	}

	public static function refundTxn($transactionID, $refundType = 'Full', $currencyID = 'SGD', $amount = 0, $note = '')
	{
		$transactionID = urlencode($transactionID);
		$refundType = urlencode($refundType);                   // or 'Partial'
		$amount;                                                // required if Partial.
		$note;                                                  // required if Partial.
		$currencyID = urlencode($currencyID);                   // or other currency ('GBP', 'EUR', 'JPY', 'CAD', 'AUD', 'USD')

		// Add request-specific fields to the request string.
		$nvpStr = "TRANSACTIONID=$transactionID&REFUNDTYPE=$refundType&CURRENCYCODE=$currencyID";

		if(isset($amount) && $amount != 0) {
			$nvpStr .= "&AMT=$amount";
		}

		if(isset($note) && $note != '') {
			$nvpStr .= "&NOTE=$note";
		}


		// Execute the API operation; see the PPHttpPost function above.
		try {
			$httpParsedResponseAr = self::doRefund('RefundTransaction', $nvpStr);
				
			if("SUCCESS" == strtoupper($httpParsedResponseAr["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($httpParsedResponseAr["ACK"])) {
				exit('Refund Completed Successfully: '.'L_SHORTMESSAGE0 ('.urldecode($httpParsedResponseAr['L_SHORTMESSAGE0']).'), L_LONGMESSAGE0 ('.urldecode($httpParsedResponseAr['L_LONGMESSAGE0']).')');
			} else  {
				exit('RefundTransaction failed: '.'L_SHORTMESSAGE0 ('.urldecode($httpParsedResponseAr['L_SHORTMESSAGE0']).'), L_LONGMESSAGE0 ('.urldecode($httpParsedResponseAr['L_LONGMESSAGE0']).')');
			}
		}catch (Exception $e)
		{
			exit("SOMETHIN WRONG WITH CALLING API");
		}
	}

	private static function doRefund($methodName_, $nvpStr_)
	{
		// Set up your API credentials, PayPal end point, and API version.
		$API_UserName = urlencode(sfConfig::get('app_paypal_credentials_username'));
		$API_Password = urlencode(sfConfig::get('app_paypal_credentials_password'));
		$API_Signature = urlencode(sfConfig::get('app_paypal_credentials_signature'));
		$API_Endpoint = "https://api-3t.paypal.com/nvp";
	  
		//	    $environment = 'sandbox';   // or 'beta-sandbox' or 'live'
		//	    if("sandbox" === $environment || "beta-sandbox" === $environment) {
		//	        $API_Endpoint = "https://api-3t.$environment.paypal.com/nvp";
		//	    }
		$version = urlencode('51.0');

		// Set the curl parameters.
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $API_Endpoint);
		curl_setopt($ch, CURLOPT_VERBOSE, 1);

		// Turn off the server and peer verification (TrustManager Concept).
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);

		// Set the API operation, version, and API signature in the request.
		$nvpreq = "METHOD=$methodName_&VERSION=$version&PWD=$API_Password&USER=$API_UserName&SIGNATURE=$API_Signature&$nvpStr_";

		// Set the request as a POST FIELD for curl.
		curl_setopt($ch, CURLOPT_POSTFIELDS, $nvpreq);

		// Get response from the server.
		$httpResponse = curl_exec($ch);

		if(!$httpResponse) {
			throw new Exception("$methodName_ failed: ".curl_error($ch).'('.curl_errno($ch).')');
		}

		// Extract the response details.
		$httpResponseAr = explode("&", $httpResponse);

		$httpParsedResponseAr = array();
		foreach ($httpResponseAr as $i => $value) {
			$tmpAr = explode("=", $value);
			if(sizeof($tmpAr) > 1) {
				$httpParsedResponseAr[$tmpAr[0]] = $tmpAr[1];
			}
		}

		if((0 == sizeof($httpParsedResponseAr)) || !array_key_exists('ACK', $httpParsedResponseAr)) {
			throw new Exception("Invalid HTTP Response for POST request($nvpreq) to $API_Endpoint.");
		}

		return $httpParsedResponseAr;
	}
}
