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

jimport('joomla.application.component.controller');
require_once( JPATH_ADMINISTRATOR . DS ."components". DS ."com_enmasse". DS ."helpers". DS ."DatetimeWrapper.class.php");
require_once( JPATH_ADMINISTRATOR . DS ."components". DS ."com_enmasse". DS ."helpers". DS ."EnmasseHelper.class.php");
require_once( JPATH_SITE . DS ."components". DS ."com_enmasse". DS ."helpers". DS ."Cart.class.php");
require_once( JPATH_SITE . DS ."components". DS ."com_enmasse". DS ."helpers". DS ."CartHelper.class.php");

class EnmasseControllerPayment extends JController
{
	function gateway()
	{
		JRequest::setVar('view', 'paygty');
		parent::display();
	}

	function returnUrl()
	{
		sleep(2);
		$msg = JText::_("PAYMENT_BEING_PROCESS_MSG");
		$link = JRoute::_("index.php?option=com_enmasse&controller=order&view=orderList", false);
		JFactory::getApplication()->redirect($link, $msg);
	}

	function notifyUrl()
	{	
		$session = & JFactory::getSession();
		$user = JFactory::getUser();
	
		$orderId 	= JRequest::getVar('orderId');
		$payClass 	= JRequest::getVar('payClass', '');
		
		$className = 'PayGty'.ucfirst($payClass);

		require_once JPATH_SITE . DS ."components". DS ."com_enmasse". DS ."helpers". DS ."payGty". DS .$payClass. DS .$className. ".class.php";

		if ( ! call_user_func_array(array($className, "validateTxn"), array($payClass)) )
		{
			echo JTEXT::_("PAYMENT_VALIDATION_FAILED");
			exit(0);
		}
		else
		{
			$payDta = call_user_func_array(array($className, "generatePaymentDetail"), array());	
			$payDetail = json_encode($payDta);		
			if($payClass=="pagseguro")
			{
				header('Content-Type: text/html; charset=ISO-8859-1');
				if(isset($_POST['Referencia']))
				{
					$orderId = $_POST['Referencia'];
					$order = JModel::getInstance('order','enmasseModel')->getById($orderId);
					if($order == null)
					{
						echo JTEXT::_("PAYMENT_ERROR_MSG") . $orderId;
						exit(0);
					}
					else if($order->status=="Unpaid") // Pass checking
					{
						switch($_POST['StatusTransacao'])
						{
							case "Completo": //Full payment
								EnmasseHelper::doNotify($orderId);			
								break;
							case "Aguardando Pagto": //Awaiting Customer Payment
								EnmasseHelper::setPendingStatusByOrderId($orderId);
								break;
							case "Aprovado": //Payment approved, awaiting compensation
								EnmasseHelper::doNotify($orderId);
								break;
							//case "Em An...": //Payment approved, under reviewing by Pagseguro
								//break;
							case "Cancelado": //Cancelled
								EnmasseHelper::setRefundedStatusByOrderId($orderId);
								break;																								
							default:
								$msg = "Muito obrigado por seu pedido!";
								$link = JRoute::_("index.php?option=com_enmasse&controller=deal", false);
								JFactory::getApplication()->redirect($link, $msg);
								break;								
						}
						JModel::getInstance('order','enmasseModel')->updatePayDetail($orderId, $payDetail);								
					}
				}	
				else					
				{
					$msg = "Muito obrigado por seu pedido!";
					$link = JRoute::_("index.php?option=com_enmasse&controller=deal", false);
					JFactory::getApplication()->redirect($link, $msg);	
				}
			}
			elseif($payClass=="payfast")
			{
				if(isset($_POST['payment_status']))
				{
					$flag	= JRequest::getVar('flag');							
					if($_POST['payment_status']=="COMPLETE")
					{
						$orderId = $_POST['m_payment_id'];
						EnmasseHelper::doNotify($orderId);
						JModel::getInstance('order','enmasseModel')->updatePayDetail($orderId, $payDetail);
					}
				}
				$msg = '';
				if($_GET['flag']=="returnUrl")
				{
					$msg = JText::_( "Thank you for purchasing!");
				}
				else if($_GET['flag']=="cancelUrl")
				{
					$msg = JText::_( "Your payment was cancelled.");
				}					
				$link = JRoute::_("index.php?option=com_enmasse&controller=deal", false);	
				JFactory::getApplication()->redirect($link, $msg);
			}
			elseif($payClass=="twocheckout")
			{
				$payGty = JModel::getInstance('payGty','enmasseModel')->getByClass($payClass);
				$attribute_config = json_decode($payGty->attribute_config);
				$accountNumber = $attribute_config->sid;
				$demo = $attribute_config->demo;
				if($demo == "Y" &&  $_REQUEST["credit_card_processed"] == "Y")
				{
					$orderId = $_REQUEST["cart_order_id"];
					EnmasseHelper::doNotify($orderId);
					JModel::getInstance('order','enmasseModel')->updatePayDetail($orderId, $payDetail);		
					$msg = JText::_( "Thank you for purchasing!");					
				} // md5 checking is only available in live payment
				elseif(demo != "Y" &&  $_REQUEST["credit_card_processed"] == "Y")
				{				
					$secretWord = $attribute_config->secret_word;
					$string_to_hash = $secretWord . $accountNumber . $_REQUEST["order_number"] . $_REQUEST["total"];
					$check_key = strtoupper(md5($string_to_hash));
					if($check_key == $_REQUEST["key"])
					{						
						$orderId = $_REQUEST["cart_order_id"];
						EnmasseHelper::doNotify($orderId);
						JModel::getInstance('order','enmasseModel')->updatePayDetail($orderId, $payDetail);
						$msg = JText::_( "Thank you for purchasing!");
					}
					else 
					{
						$msg = JText::_( "Can't verify your payment! Please contact Administrator!");
					}
				}
				$link = JRoute::_("index.php?option=com_enmasse&controller=deal", false);
				JFactory::getApplication()->redirect($link, $msg);
			}
			elseif($payClass=="authorizenet")
			{
				if($_POST['approved']=='true');
				{
					$orderId = $_POST['invoice_number'];
					EnmasseHelper::doNotify($orderId);
					JModel::getInstance('order','enmasseModel')->updatePayDetail($orderId, $payDetail);					
					$msg = JText::_( "Thank you for purchasing! Your transaction ID is " . $_POST['transaction_id'] . ".");
				}
				$link = JRoute::_("index.php?option=com_enmasse&controller=deal", false);
				JFactory::getApplication()->redirect($link, $msg);
			}
			elseif($payClass=="ewayhosted")
			{			
				if($_REQUEST['trxnstatus']=='true');
				{
					$orderId = $_REQUEST['orderId'];
					EnmasseHelper::doNotify($orderId);
					JModel::getInstance('order','enmasseModel')->updatePayDetail($orderId, $payDetail);					
					$msg = JText::_( "Thank you for purchasing!");
				}
				$link = JRoute::_("index.php?option=com_enmasse&view=orderlist", false);
				JFactory::getApplication()->redirect($link, $msg);
			}			
			elseif($payClass=="paypal")
			{
				$payDta = call_user_func_array(array($className, "generatePaymentDetail"), array());	
				$payDetail = json_encode($payDta);
				JModel::getInstance('order','enmasseModel')->updatePayDetail($orderId, $payDetail);
				
				$link = JRoute::_("index.php?option=com_enmasse&controller=payment&task=doNotify&$postData&orderId=$orderId", false);
				JFactory::getApplication()->redirect($link);
			}	
			elseif($payClass=="moneybookers")
			{				
				if(isset($_POST['transaction_id']) && $_POST['status'] == 2)
				{
                    $orderId = $_POST['transaction_id'];
                    if(EnmasseHelper::getOrderStatusByOrderId($orderId) == "Unpaid")
                    {
                        EnmasseHelper::doNotify($orderId);
                        JModel::getInstance('order','enmasseModel')->updatePayDetail($orderId, $payDetail); 
                    }
				}               
            }
			elseif($payClass=="ogone")
			{				
				$aData = array();
				foreach($_REQUEST as $key=>$value)
				{
					$aData[strtoupper($key)] = $value;
				}			
				$orderId = $aData['ORDERID'];
				if($aData['STATUS'] == '5' || $aData['STATUS'] == '9')
				{
					EnmasseHelper::doNotify($orderId);
					JModel::getInstance('order','enmasseModel')->updatePayDetail($orderId, $payDetail);
					$sMessage = JText::_('PAYMENT_BEING_PROCESS_MSG');					
				}
				else
				{
					$sMessage = JTEXT::_("PAYMENT_VALIDATION_FAILED");	
				}
				$link = JRoute::_("/", false);
				JFactory::getApplication()->redirect($link, $sMessage);
            }
            elseif($payClass=="googlecheckout")
            {
            	$merchantId = '624370439819433';
            	$merchantKey = 'r86DDFDkmjzrqrr7kW_NMQ';
            	$authorization = base64_encode($merchantId.':'.$merchantKey);
            	header('Authorization: Basic '.$authorization);
            	header('Content-Type: application/xml;charset=UTF-8');
            	header('Accept: application/xml;charset=UTF-8');
            	
            	$serial_number = $_REQUEST['serial-number'];
            	
				$orderId = $session->get( 'newOrderId', 0 );
				if (isset($orderId) && $orderId) {
	            	EnmasseHelper::doNotify($orderId);
	            	JModel::getInstance('order','enmasseModel')->updatePayDetail($orderId, $payDetail);
				}
            	
            	echo '<?xml version="1.0" encoding="UTF-8"?>
            			<notification-acknowledgment xmlns="http://checkout.google.com/schema/2" serial-number="'.$serial_number.'"/>';
            	exit;
            }
			elseif($payClass=="realex")
			{			
				if($_REQUEST['RESULT']=='00');
				{
					$orderId = $_REQUEST['ORDER_ID'];
					EnmasseHelper::doNotify($orderId);
					JModel::getInstance('order','enmasseModel')->updatePayDetail($orderId, $payDetail);					
					$msg = JText::_( "Thank you for purchasing!");
				}
				$link = JRoute::_("index.php?option=com_enmasse&view=orderlist", false);
				JFactory::getApplication()->redirect($link, $msg);
			}         
		}
		$session->clear('newOrderId');
	}

	function doNotify()
	{		
		$orderId = JRequest::getVar("orderId", null);
		
		$order = JModel::getInstance('order','enmasseModel')->getById($orderId);
		if($order == null)
		{
			echo JTEXT::_("PAYMENT_ERROR_MSG") . $orderId;
			exit(0);
		}
		else if($order->status=="Unpaid") // Pass checking
		{
			EnmasseHelper::doNotify($orderId);
		}

		$msg = JTEXT::_("PAYMENT_SUCCESS");

		$link = JRoute::_("index.php?option=com_enmasse&controller=deal", false);
		JFactory::getApplication()->redirect($link, $msg);
	}

	function cancelUrl()
	{
		$msg = JText::_( "CANCEL_TRANSACTION");
		$link = JRoute::_("index.php?option=com_enmasse&controller=deal", false);
		JFactory::getApplication()->redirect($link, $msg);
	}

}
?>