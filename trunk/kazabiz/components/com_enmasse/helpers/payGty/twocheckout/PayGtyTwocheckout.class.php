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

class PayGtyTwocheckout {

	public static function returnStatus()
	{
	  $status ->coupon = 'Free';
	  $status ->order  = 'Unpaid';
	  return $status;
	}
	    
	public static function checkConfig($payGty)
	{
		$attribute_config = json_decode($payGty->attribute_config);
		if ( !isset($attribute_config->sid) || trim($attribute_config->sid) == "")
		{
			return false;
		}	
		return true;
	}    
	
	public static function validateTxn($payClass)
	{
        $payGty = JModel::getInstance('payGty','enmasseModel')->getByClass("twocheckout");
		$attribute_config = json_decode($payGty->attribute_config);
        
        $data = array();
        
        foreach ($_POST as $field=>$value)
        {
            $data["$field"] = $value;
        }

        $vendorNumber   = ($data["vendor_number"] != '') ? $data["vendor_number"] : $data["sid"];
        $orderNumber    = $data["order_number"];
        $orderTotal     = $data["total"];

        // If demo mode, the order number must be forced to 1
        if($attribute_config->demo == "Y" || $data['demo'] == 'Y')
        {
            $orderNumber = "1";
        }

        // Calculate md5 hash as 2co formula: md5(secret_word + vendor_number + order_number + total)
        $key = strtoupper(md5($attribute_config->secret_word . $vendorNumber . $orderNumber . $orderTotal));

        // verify if the key is accurate
        if($data["key"] == $key || $data["x_MD5_Hash"] == $key)
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
		$paymentDta = array();
		
		$paymentDta["order_number"]             = $_REQUEST['order_number'];
        $paymentDta["invoice_id"]               = $_REQUEST['invoice_id'];
        $paymentDta["pay_method"]               = $_REQUEST['pay_method'];
        $paymentDta["card_holder_name"]         = $_REQUEST['card_holder_name'];
        $paymentDta["total"]                    = $_REQUEST['total']; 
		return $paymentDta;		
	}	
}
 
?>
