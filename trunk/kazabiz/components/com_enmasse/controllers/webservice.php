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
define('EM_WS_TYPE_OK', 'OK');
define('EM_WS_TYPE_ERROR', 'ERROR');
define('EM_WS_FIELD_TYPE', 'Type');
define('EM_WS_FIELD_MESSAGE', 'Message');
define('EM_WS_FIELD_DATA', 'Data');

require_once(JPATH_ADMINISTRATOR . DS . "components" . DS . "com_enmasse" . DS . "helpers" . DS . "EnmasseHelper.class.php");
class EnmasseControllerWebservice extends JController
{
	
	public function display()
	{
		
	}
	
	public function login()
	{
		$result = array();
		$username = JRequest::getVar('username', '', 'post');
		$password = JRequest::getVar('password', '', 'post');
		$credential = array('username' =>$username, 'password' => $password);
		$app = JFactory::getApplication();
		$bValid = $app->login($credential);
		if($bValid)
		{
			$user = JFactory::getUser();
			//check whether this user is merchant or not
			$oMerchant = JModel::getInstance('Merchant', 'EnmasseModel')->getByUserName($username);
			if(!$oMerchant)
			{
				$result[EM_WS_FIELD_TYPE] = EM_WS_TYPE_ERROR;
				$result[EM_WS_FIELD_DATA] = "";
				$result[EM_WS_FIELD_MESSAGE] = JText::_("MERCHANT_WS_ACCOUNT_NOT_IS_MERCHANT");
				echo json_encode($result);die;
			}
			$emSesion = JModel::getInstance('WebserviceSession','EnmasseModel');
			$token = $emSesion->createSession($user->id, $oMerchant->id);
			$user->token = $token;
			$result[EM_WS_FIELD_TYPE] = EM_WS_TYPE_OK;
			$result[EM_WS_FIELD_MESSAGE] = JText::_("MERCHANT_WS_LOGIN_SUCCESSFUL");
			$result[EM_WS_FIELD_DATA] = $user;
		}else 
		{
			$oError = array_pop($app->getMessageQueue());
			$result[EM_WS_FIELD_TYPE] = EM_WS_TYPE_ERROR;
			$result[EM_WS_FIELD_MESSAGE] = $oError['message'];
			$result[EM_WS_FIELD_DATA] = "";
		}
		echo json_encode($result);die;
	}
	
	public function validateAccount()
	{
		$result = array();
		$username = JRequest::getVar('username', '', 'post');
		$password = JRequest::getVar('password', '', 'post');
		$credential = array('username' =>$username, 'password' => $password);
		$app = JFactory::getApplication();
		$bValid = $app->login($credential);
		if($bValid)
		{
			//check whether this user is merchant or not
			$oMerchant = JModel::getInstance('Merchant', 'EnmasseModel')->getByUserName($username);
			if(!$oMerchant)
			{
				$result[EM_WS_FIELD_TYPE] = EM_WS_TYPE_ERROR;
				$result[EM_WS_FIELD_DATA] = "";
				$result[EM_WS_FIELD_MESSAGE] = JText::_("MERCHANT_WS_ACCOUNT_NOT_IS_MERCHANT");
				echo json_encode($result);die;
			}
			$result[EM_WS_FIELD_TYPE] = EM_WS_TYPE_OK;
			$result[EM_WS_FIELD_MESSAGE] = JText::_("MERCHANT_WS_ACCOUNT_IS_VALID");
			$result[EM_WS_FIELD_DATA] = "";
		}else 
		{
			$oError = array_pop($app->getMessageQueue());
			$result[EM_WS_FIELD_TYPE] = EM_WS_TYPE_ERROR;
			$result[EM_WS_FIELD_MESSAGE] = $oError['message'];
			$result[EM_WS_FIELD_DATA] = "";
		}
		echo json_encode($result);die;
	}
	
	public function logout()
	{
		$result = array();
		$token = JRequest::getVar('token', '', 'post');
		$bValid = JModel::getInstance('WebserviceSession','EnmasseModel')->deleteSession($token);
		if($bValid)
		{
			$result[EM_WS_FIELD_TYPE] = EM_WS_TYPE_OK;
			$result[EM_WS_FIELD_MESSAGE] = JText::_("MERCHANT_WS_LOGOUT_SUCCESSFUL");
			$result[EM_WS_FIELD_DATA] = "";
		}else 
		{
			$result[EM_WS_FIELD_TYPE] = EM_WS_TYPE_ERROR;
			$result[EM_WS_FIELD_MESSAGE] = JText::_("MERCHANT_WS_SESSION_EXPIRED");
			$result[EM_WS_FIELD_DATA] = "";
		}
	}
	
	public function getCouponDetail()
	{
		//authenticate user
		$this->authenticate();
		$result = array();
		$invtyName = JRequest::getVar('qr_code', '', 'post');
		
		$oInvty = JModel::getInstance('invty', 'EnmasseModel')->getByName($invtyName);
		if($oInvty)
		{
			//get buyer detail
			$oOrder = JModel::getInstance('order', 'EnmasseModel')->getOrderByOrderItemId($oInvty->order_item_id);
			$buyer = json_decode($oOrder->buyer_detail);
			$data = array();
			$data['buyer_name'] = $buyer->name;
			$data['buyer_email'] = $buyer->email;
			$data['deal_name'] = $oOrder->deal_name;
			$data['purchase_date'] = $oOrder->created_at;
			$data['order_comment'] = $oOrder->description;
			$data['coupon_code'] = $oInvty->name;
			$result[EM_WS_FIELD_TYPE] = EM_WS_TYPE_OK;
			$result[EM_WS_FIELD_MESSAGE] = "";
			$result[EM_WS_FIELD_DATA] = $data;
		}else 
		{
			$result[EM_WS_FIELD_TYPE] = EM_WS_TYPE_ERROR;
			$result[EM_WS_FIELD_MESSAGE] = JText::_("MERCHANT_WS_INVALID_COUPON_CODE");
			$result[EM_WS_FIELD_DATA] = "";
		}
		//update user session
		$this->updateSession();
		//return result
		echo json_encode($result);
		die;
	}
	
		
	public function markUsed()
	{
		//authenticate user
		$oSession = $this->authenticate();
		$result = array();
		$invtyName = JRequest::getVar('qr_code', '', 'post');
		
		if(!EnmasseHelper::checkCupponOfMerchant($invtyName, $oSession->merchant_id) )
		{
			$result[EM_WS_FIELD_TYPE] = EM_WS_TYPE_ERROR;
			$result[EM_WS_FIELD_MESSAGE] = JText::_("MERCHANT_WS_INVALID_COUPON_CODE");
			$result[EM_WS_FIELD_DATA] = "";
		}else 
		{
			$oInvty = JModel::getInstance('invty','enmasseModel')->getByName($invtyName);
			$orderItem = JModel::getInstance('orderItem','enmasseModel')->getById($oInvty->order_item_id);
			if($orderItem->status != "Delivered")
			{
				$result[EM_WS_FIELD_TYPE] = EM_WS_TYPE_ERROR;
				$result[EM_WS_FIELD_MESSAGE] = JText::_('COUPON_STATUS_ERROR'). "(". $oInvty->name ." - ". $orderItem->status .")";
				$result[EM_WS_FIELD_DATA] = "";
			}
			if ($oInvty->status == "Used")
			{
				$result[EM_WS_FIELD_TYPE] = EM_WS_TYPE_ERROR;
				$result[EM_WS_FIELD_MESSAGE] = JText::_('COUPON_ALREADY_IN_USE'). '('.$oInvty->name.")";
				$result[EM_WS_FIELD_DATA] = "";
			}else
			{
				JModel::getInstance('invty','enmasseModel')->updateStatusByName($invtyName,"Used");
				$result[EM_WS_FIELD_TYPE] = EM_WS_TYPE_OK;
				$result[EM_WS_FIELD_MESSAGE] = JText::_('COUPON_STATUS_UPDATE'). '('.$oInvty->name.")";
				$result[EM_WS_FIELD_DATA] = "";
			}
		}
		//update user session
		$this->updateSession();
		//return result
		echo json_encode($result);
		die;
	}
	
	public function markUnused()
	{
		//authenticate user
		$oSession = $this->authenticate();
		$result = array();
		$invtyName = JRequest::getVar('qr_code', '', 'post');
		
		if(!EnmasseHelper::checkCupponOfMerchant($invtyName, $oSession->merchant_id))
		{
			$result[EM_WS_FIELD_TYPE] = EM_WS_TYPE_ERROR;
			$result[EM_WS_FIELD_MESSAGE] = JText::_("MERCHANT_WS_INVALID_COUPON_CODE");
			$result[EM_WS_FIELD_DATA] = "";
		}else 
		{
			$oInvty = JModel::getInstance('invty','enmasseModel')->getByName($invtyName);
			$orderItem = JModel::getInstance('orderItem','enmasseModel')->getById($oInvty->order_item_id);
			if($orderItem->status != "Delivered")
			{
				$result[EM_WS_FIELD_TYPE] = EM_WS_TYPE_ERROR;
				$result[EM_WS_FIELD_MESSAGE] = JText::_('COUPON_STATUS_ERROR'). "(". $oInvty->name ." - ". $orderItem->status .")";
				$result[EM_WS_FIELD_DATA] = "";
			}
			if ($oInvty->status == "Taken")
			{
				$result[EM_WS_FIELD_TYPE] = EM_WS_TYPE_ERROR;
				$result[EM_WS_FIELD_MESSAGE] = JText::_('COUPON_ALREADY_IN_TAKEN'). '('.$oInvty->name.")";
				$result[EM_WS_FIELD_DATA] = "";
			}else
			{
				JModel::getInstance('invty','enmasseModel')->updateStatusByName($invtyName,"Taken");
				$result[EM_WS_FIELD_TYPE] = EM_WS_TYPE_OK;
				$result[EM_WS_FIELD_MESSAGE] = JText::_('COUPON_STATUS_UPDATE'). '('.$oInvty->name.")";
				$result[EM_WS_FIELD_DATA] = "";
			}
		}
		//update user session
		$this->updateSession();
		//return result
		echo json_encode($result);
		die;
	}
	
	private function authenticate()
	{
		$token = JRequest::getVar('token','', 'post');
		$result = array();
		$oSession = JModel::getInstance('WebserviceSession','EnmasseModel')->getByToken($token);
		
		if($oSession)
		{
			if(strtotime($oSession->expired_at) < strtotime($oSession->curtime))
			{
				$result[EM_WS_FIELD_TYPE] = EM_WS_TYPE_ERROR;
				$result[EM_WS_FIELD_MESSAGE] = JText::_("MERCHANT_WS_SESSION_EXPIRED");;
				$result[EM_WS_FIELD_DATA] = "";
				echo json_encode($result);
				die;//do not continue processing 
			}else
			{
				return $oSession;
			}
		}else 
		{
			$result[EM_WS_FIELD_TYPE] = EM_WS_TYPE_ERROR;
			$result[EM_WS_FIELD_MESSAGE] = JText::_("MERCHANT_WS_NOT_LOGGED_IN");
			$result[EM_WS_FIELD_DATA] = "";
			echo json_encode($result);
			die;//do not continue processing 
		}
	}
	
	private function updateSession()
	{
		$token = JRequest::getVar('token','', 'post');
		JModel::getInstance('WebserviceSession','EnmasseModel')->updateSessionLifetime($token);
	}
}

