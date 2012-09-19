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

class PayGtyMoneybookers
{        
    private $arReasons = array();
    
    public function __construct() {
        // Explanation or error codes
        $this->arReasons['01'] = 'Referred';
        $this->arReasons['02'] = 'Invalid Merchant Number';
        $this->arReasons['03'] = 'Pick-up card';
        $this->arReasons['04'] = 'Authorisation Declined';
        $this->arReasons['05'] = 'Other Error';
        $this->arReasons['06'] = 'CVV is mandatory, but not set or invalid';
        $this->arReasons['07'] = 'Approved authorisation, honour with identification';
        $this->arReasons['08'] = 'Delayed Processing';
        $this->arReasons['09'] = 'Invalid Transaction';
        $this->arReasons['10'] = 'Invalid Currency';
        $this->arReasons['11'] = 'Invalid Amount/Available Limit Exceeded/Amount too high';
        $this->arReasons['12'] = 'Invalid credit card or bank account';
        $this->arReasons['13'] = 'Invalid Card Issuer';
        $this->arReasons['14'] = 'Annulation by client';
        $this->arReasons['15'] = 'Duplicate transaction';
        $this->arReasons['16'] = 'Acquirer Error';
        $this->arReasons['17'] = 'Reversal not processed, matching authorisation not found';
        $this->arReasons['18'] = 'File Transfer not available/unsuccessful';
        $this->arReasons['19'] = 'Reference number error';
        $this->arReasons['20'] = 'Access Denied';
        $this->arReasons['21'] = 'File Transfer failed ';       
        $this->arReasons['22'] = 'Format Error';
        $this->arReasons['23'] = 'Unknown Acquirer';
        $this->arReasons['24'] = 'Card expired';
        $this->arReasons['25'] = 'Fraud Suspicion';    
        $this->arReasons['26'] = 'Security code expired';
        $this->arReasons['27'] = 'Requested function not available  ';
        $this->arReasons['28'] = 'Lost/Stolen card';
        $this->arReasons['29'] = 'Stolen card, Pick up';    
        $this->arReasons['30'] = 'Duplicate Authorisation';
        $this->arReasons['31'] = 'Limit Exceeded';
        $this->arReasons['32'] = 'Invalid Security Code';
        $this->arReasons['33'] = 'Unknown or Invalid Card/Bank account';       
        $this->arReasons['34'] = 'Illegal Transaction';
        $this->arReasons['35'] = 'Transaction Not Permitted';
        $this->arReasons['36'] = 'Card blocked in local blacklist';
        $this->arReasons['37'] = 'Restricted card/bank account';    
        $this->arReasons['38'] = 'Security Rules Violation';
        $this->arReasons['39'] = 'The transaction amount of the referencing transaction is higher than the transaction amount of 
the original transaction';       
        $this->arReasons['40'] = 'Transaction frequency limit exceeded, override is possible';
        $this->arReasons['41'] = 'Incorrect usage count in the Authorisation System exceeded';
        $this->arReasons['42'] = 'Card blocked';
        $this->arReasons['43'] = 'Rejected by Credit Card Issuer';     
        $this->arReasons['44'] = 'Card Issuing Bank or Network is not available';
        $this->arReasons['45'] = 'The card type is not processed by the authorisation centre / Authorisation System has determined';         
    }


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
		if($_POST['status']==true && isset($_POST['transaction_id']))
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	public static function generatePaymentDetail()
	{
		$paymentDta = $_POST;
		return $paymentDta;
	}
    
    public function lookUpFailedReasonCode($nCode)
    {
        $sReason = $this->arReasons[$nCode];
        if($sReason=='')
        {
            return 'Unknown error';           
        }
        else
        {
            return $sReason;
        }
    }
}