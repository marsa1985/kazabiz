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
header('Content-Type: text/html; charset=ISO-8859-1');

class PagSeguroNpi
{	
	private $timeout = 20; // Timeout in seconds
	
	public function notificationPost()
	{
		// Get Token from database
		$query = "SELECT attributes, attribute_config FROM #__enmasse_pay_gty WHERE class_name='pagseguro'";
		$db = JFactory::getDBO();
		$db->setQuery($query);
		$result = $db->loadRow();
			
		$attribute_list 	= explode(",",$result[0]);
		$attribute_obj 		= json_decode($result[1]);
		
		$token = "";
		
		for ($i=0; $i < count($attribute_list); $i++)
		{
			$count = $i + 1;
			$title = $attribute_list[$i];
			$value = $attribute_obj->$title;
			if($title=="token")
			{
				$token = $value;
			}
		}
		
		$postdata = 'Comando=validar&Token='.$token;
		
		foreach ($_POST as $key => $value)
		{
			$valued    = $this->clearStr($value);
			$postdata .= "&$key=$valued";
		}
		return $this->verify($postdata);
	}
	
	private function clearStr($str)
	{
		if (!get_magic_quotes_gpc())
		{
			$str = addslashes($str);
		}
		return $str;
	}
	
	private function verify($data) {
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, "https://pagseguro.uol.com.br/pagseguro-ws/checkout/NPI.jhtml");
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_TIMEOUT, $this->timeout);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		$result = trim(curl_exec($curl));
		curl_close($curl);
		return $result;
	}

}

class PayGtyPagseguro {
	public static function returnStatus()
	{
	  $status ->coupon = 'Free';
	  $status ->order  = 'Unpaid';
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
	
	public static function validateTxn($payClass)
	{
		return true;
		if(count($_POST)>0)
		{
			$npi = new PagSeguroNpi();
			$result = $npi->notificationPost();
			$transacaoID = isset($_POST['TransacaoID']) ? $_POST['TransacaoID'] : '';
	
			if ($result=="VERIFICADO")
			{
				//Validated by Pagseguro
				return true;
			}
			else if ($result=="FALSO")
			{
				//Failed
				return false;
			}
			else
			{
				//Error in the integration with Pagseguro.
				return false;
			}
		}
	}
	
	public static function generatePaymentDetail()
	{
		$paymentDta = array();
		
		$paymentDta['TransacaoID'] = $_POST['TransacaoID'];
		$paymentDta['TipoPagamento'] = $_POST['TipoPagamento'];
		$paymentDta['StatusTransacao'] = $_POST['StatusTransacao'];

		return $paymentDta;		
	}	
}
?>