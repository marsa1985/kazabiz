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

class PayGtyRealex {

	public static function returnStatus()
	{
	  $status ->coupon = 'Free';
	  $status ->order  = 'Unpaid';
	  return $status;
	}
	    
	public static function checkConfig($payGty)
	{
		$attribute_config = json_decode($payGty->attribute_config);
		if ( !isset($attribute_config->MERCHANT_ID) || trim($attribute_config->MERCHANT_ID) == "")
		{
			return false;
		}
		if ( !isset($attribute_config->SECRET) || trim($attribute_config->SECRET) == "")
		{
			return false;
		}
		return true;
	}    
	
	public static function validateTxn($payClass)
	{
        $payGty = JModel::getInstance('payGty','enmasseModel')->getByClass("realex");
		$attribute_config = json_decode($payGty->attribute_config);
        
        $data = array();
        
        foreach ($_POST as $field=>$value)
        {
            $data["$field"] = $value;
        }
        
        // check hash
        /*
        $session = & JFactory::getSession();
        echo "<br/>sessionHash:".$session->get("realExHash");
        if ( $data["SHA1HASH"] != $session->get("realExHash")) {
        	return false;
        }
        */
        
        // check AUTHCODE
        if ( !isset($data["AUTHCODE"]) || $data["AUTHCODE"] == '') {
        	return false;
        }
        
		return true;
	}
	
	public static function generatePaymentDetail()
	{
		
		$paymentDta = array();
		
		$paymentDta["MERCHANT_ID"]             = $_REQUEST['MERCHANT_ID'];
        $paymentDta["ACCOUNT"]               = $_REQUEST['ACCOUNT'];
        $paymentDta["ORDER_ID"]               = $_REQUEST['ORDER_ID'];
        $paymentDta["AUTHCODE"]         = $_REQUEST['AUTHCODE'];
        $paymentDta["RESULT"]                    = $_REQUEST['RESULT']; 
        $paymentDta["MESSAGE"]                    = $_REQUEST['MESSAGE']; 
        
		return $paymentDta;		
		
	}	
}
 
?>
