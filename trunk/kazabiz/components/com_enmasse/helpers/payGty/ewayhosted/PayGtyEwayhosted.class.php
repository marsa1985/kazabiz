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

class PayGtyEwayHosted {

	public static function returnStatus()
	{
	  $status ->coupon = 'Free';
	  $status ->order  = 'Unpaid';
	  return $status;
	}
	    
	public static function checkConfig($payGty)
	{
		$attribute_config = json_decode($payGty->attribute_config);
		if (!isset($attribute_config->ewayCustomerID))
		{
			return false;
		}		
		return true;
	}    
	
	public static function validateTxn($payClass)
	{
		if($_GET['trxnstatus']==true && isset($_GET['trxnumber']))
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

		$paymentDta["trxnauthcode"]	= $_GET['trxnauthcode'];
		$paymentDta["trxnstatus"]	= $_GET['trxnstatus'];
		$paymentDta["trxnumber"]	= $_GET['trxnumber'];
		$paymentDta["returnamount"]	= $_GET['returnamount'];
						
		return $paymentDta;		
	}	
}
 
?>
