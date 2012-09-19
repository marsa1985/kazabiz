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
require_once( JPATH_ADMINISTRATOR . DS ."components". DS ."com_enmasse". DS ."helpers". DS ."EnmasseHelper.class.php");

class EnmasseControllerMerchant extends JController
{	
	function display($cachable = false, $urlparams = false)
	{
		$this->checkAccess();
		
		JRequest::setVar('view', 'merchant');
		JRequest::setVar('task', 'dealCouponMgmt');
		parent::display();
	}
	
	function dealCouponMgmt()
	{
		$this->checkAccess();
		
		JRequest::setVar('view', 'merchant');
		JRequest::setVar('task', 'dealCouponMgmt');
		parent::display();
	}
	
	function update()
	{
		$dAuthorId = $this->checkAccess();
		
		$action = JRequest::getVar('newStatus');
		$coupon = JRequest::getVar('coupon');
		//Check whether coupon belong this merchant or not
		if(! EnmasseHelper::checkCupponOfMerchant($coupon, $dAuthorId))
		{
			$msg = JText::_( 'MERCHANT_INVALID_COUPON_SERIAL');
			$this->setRedirect("index.php?option=com_enmasse&controller=merchant&task=dealCouponMgmt", $msg, "error");
			return;
		}
		$invty = JModel::getInstance('invty','enmasseModel')->getByName($coupon);
		if(!$invty)
		{
			$msg = JText::_( 'MERCHANT_INVALID_COUPON_SERIAL');
			$this->setRedirect("index.php?option=com_enmasse&controller=merchant&task=dealCouponMgmt", $msg, "error");
		}
		else
		{
			$orderItem = JModel::getInstance('orderItem','enmasseModel')->getById($invty->order_item_id);
			
			if($orderItem->status != "Delivered")
			{
				$msg = JText::_('COUPON_STATUS_ERROR'). "(". $invty->name ." - ". $orderItem->status .")";
				$this->setRedirect("index.php?option=com_enmasse&controller=merchant&task=dealCouponMgmt&filter[deal_id]=$orderItem->pdt_id", $msg, 'error');
			
			}
			elseif($action=="Used")
			{
				$invty = JModel::getInstance('invty','enmasseModel')->getByName($coupon);
				if($invty->status=="Used")
				{
					$msg = JText::_( 'COUPON_ALREADY_IN_USE'). '('.$invty->name.")";
					$this->setRedirect("index.php?option=com_enmasse&controller=merchant&task=dealCouponMgmt&filter[deal_id]=$orderItem->pdt_id", $msg, 'error');
				}		
				else
				{
					JModel::getInstance('invty','enmasseModel')->updateStatusByName($coupon,"Used");
					$msg = JText::_( 'COUPON_STATUS_UPDATE'). '('.$invty->name.")";
					$this->setRedirect("index.php?option=com_enmasse&controller=merchant&task=dealCouponMgmt&filter[deal_id]=$orderItem->pdt_id", $msg);
				}
			}
			else
			{
				JModel::getInstance('invty','enmasseModel')->updateStatusByName($coupon,"Taken");
				$msg = JText::_( 'COUPON_STATUS_UPDATE').'('.$invty->name.")";
				$this->setRedirect("index.php?option=com_enmasse&controller=merchant&task=dealCouponMgmt&filter[deal_id]=$orderItem->pdt_id", $msg);
					
			}
		}
	}
	
	public function login()
	{
		$app = JFactory::getApplication();
		$return = array();
		// Populate the data array:
		$data = array();
		$data['username'] = JRequest::getVar('username', '', 'method', JREQUEST_ALLOWRAW);
		$data['password'] = JRequest::getString('password', '', 'method', JREQUEST_ALLOWRAW);

		// Get the log in credentials.
		$credentials = array();
		$credentials['username'] = $data['username'];
		$credentials['password'] = $data['password'];

		// Perform the log in.
		$error = $app->login($credentials, $options);

		// Check if the log in succeeded.
		if (!JError::isError($error)) {
			$merchant = JModel::getInstance('merchant','enmasseModel')->getByUserName(JFactory::getUser()->get('username'));
			if($merchant)
			{
				JFactory::getSession()->set('merchantId', $merchant->id);
				JFactory::getSession()->close();
				$return['success'] = true;
				$return['token'] = JFactory::getSession()->getName();
				
				echo json_encode($return);
				die;
			}
		}
		//login fail
		$return['success'] = false;
		$return['message'] = $app->getError();
		echo json_encode($return);
		die;
	}
	
	public function payOut()
	{
		$nAuthorId = $this->checkAccess();
		$filter = JRequest::getVar('filter', array(), 'method', 'array');
		$nDealId = isset($filter['deal_id'])? $filter['deal_id'] : '';
		//check deal belong to this merchant or not
		if(empty($nDealId) || ! JModel::getInstance('deal', 'EnmasseModel')->checkMerchantOfDeal($nAuthorId, $nDealId))
		{
			$msg = JText::_('INVALID_DEAL_ID');
			$link = JRoute::_('index.php?option=com_enmasse&controller=merchant&task=dealCouponMgmt');
			JFactory::getApplication()->redirect($link, $msg, 'error');
		}
		//get coupon list with status is Not_Paid_Out of this deal
		$arCoupon = JModel::getInstance('invty', 'EnmasseModel')->getNotPaidOutCouponByDealId($nDealId);
		
		if(empty($arCoupon))
		{
			$msg = JText::_('MERCHANT_ALREADY_PAID_OUT');
			$link = JRoute::_('index.php?option=com_enmasse&controller=merchant&task=dealCouponMgmt');
			JFactory::getApplication()->redirect($link, $msg, 'error');
		}
		
		//update status of all coupon of the deal to 'Should_Be_Paid_Out'
		$arCouponId = array_keys($arCoupon);
		JModel::getInstance('invty', 'EnmasseModel')->payOutCoupons($arCouponId);
		
		//send message to admin
		$mailer = JFactory::getMailer();
		
		$oMer = JModel::getInstance('merchant','enmasseModel')->getByUserName(JFactory::getUser()->get('username'));
		$oDeal = JModel::getInstance('deal','enmasseModel')->getById($nDealId);
		
		$sender = array(
            JFactory::getUser()->email,
            $oMer->name
        );
        $mailto = EnmasseHelper::getSetting()->customer_support_email;
        
        $recipient = array($mailto); //admin email was config in enmasse_setting;
		$subject = JText::sprintf('MERCHANT_PAY_OUT_EMAIL_SUBJECT', $oDeal->deal_code);
		$body    = JText::sprintf('MERCHANT_PAY_OUT_EMAIL_BODY', $oMer->name, $oDeal->name);
        $mailer->setSubject($subject);
        $mailer->setBody($body);

        $mailer->addRecipient($recipient);
        $mailer->setSender($sender);
        $send = &$mailer->Send();
        
		//update successful, redirect to merchant home page
		$msg = JText::_('MERCHANT_PAY_OUT_SUCCESS');
		$link = JRoute::_("index.php?option=com_enmasse&controller=merchant&task=dealCouponMgmt&filter[deal_id]=$nDealId");
		JFactory::getApplication()->redirect($link, $msg);
	}
	
	public function payOutCoupons()
	{
		$nAuthorId = $this->checkAccess();
		$arCouponId = JRequest::getVar('cid', array(), 'post', 'array');
		$filter = JRequest::getVar('filter', array(), 'method', 'array');
		$nDealId = isset($filter['deal_id'])? $filter['deal_id'] : '';
		//check deal belong to this merchant or not
		if(empty($nDealId) || ! JModel::getInstance('deal', 'EnmasseModel')->checkMerchantOfDeal($nAuthorId, $nDealId))
		{
			$msg = JText::_('INVALID_DEAL_ID');
			$link = 'index.php?option=com_enmasse&controller=merchant&task=dealCouponMgmt';
			JFactory::getApplication()->redirect($link, $msg, 'error');
		}
		//get coupon list with status is Not_Paid_Out of this deal
		$arCoupon = JModel::getInstance('invty', 'EnmasseModel')->getNotPaidOutCouponByDealId($nDealId);
		
		if(empty($arCoupon))
		{
			$msg = JText::_('MERCHANT_COUPONS_ALREADY_IN_PROCESS');
			$link = JRoute::_("index.php?option=com_enmasse&controller=merchant&task=dealCouponMgmt&filter[deal_id]=$nDealId");
			JFactory::getApplication()->redirect($link, $msg, 'error');
		}
		
		//calculate the coupon ids that have status is "Not_Paid_Out"
		//we must compare with cid array variable because merchant can also selected the paid out coupons at client
		
		$arNPOId = array_keys($arCoupon);
		$arCouponId = array_intersect($arCouponId, $arNPOId);
		
		//update status of calculated coupon of the deal to 'Should_Be_Paid_Out'
		if(empty($arCouponId))
		{
			$msg = JText::_('MERCHANT_COUPONS_ALREADY_IN_PROCESS');
			$link = JRoute::_("index.php?option=com_enmasse&controller=merchant&task=dealCouponMgmt&filter[deal_id]=$nDealId");
			JFactory::getApplication()->redirect($link, $msg, 'error');
		}else
		{
			JModel::getInstance('invty', 'EnmasseModel')->payOutCoupons($arCouponId);
		}
				
		//send message to admin
		$mailer = JFactory::getMailer();
		
		$oMer = JModel::getInstance('merchant','enmasseModel')->getByUserName(JFactory::getUser()->get('username'));
		$oDeal = JModel::getInstance('deal','enmasseModel')->getById($nDealId);
		
		$sender = array(
            JFactory::getUser()->email,
            $oMer->name
        );
        $mailto = EnmasseHelper::getSetting()->customer_support_email;
        
        $recipient = array($mailto); //admin email was config in enmasse_setting;
		$subject = JText::sprintf('MERCHANT_PAY_OUT_EMAIL_SUBJECT', $oDeal->deal_code);
		$body    = JText::sprintf('MERCHANT_PAY_OUT_EMAIL_BODY', $oMer->name, $oDeal->name);
        $mailer->setSubject($subject);
        $mailer->setBody($body);

        $mailer->addRecipient($recipient);
        $mailer->setSender($sender);
        $send = &$mailer->Send();
        
		//update successful, redirect to merchant home page
		$msg = JText::_('MERCHANT_COUPONS_PAY_OUT_SUCCESS');
		$link = JRoute::_("index.php?option=com_enmasse&controller=merchant&task=dealCouponMgmt&filter[deal_id]=$nDealId");
		JFactory::getApplication()->redirect($link, $msg);
	}
//---------------------------------------------
	
	private function checkAccess()
	{
		if (JFactory::getUser()->get('guest'))
		{			
			$msg = JText::_( "MERCHANT_PLEASE_LOGIN_BEFORE");
			$redirectUrl = base64_encode("index.php?option=com_enmasse&controller=merchant&task=dealCouponMgmt");  
			$version = new JVersion;
            $joomla = $version->getShortVersion();
            if(substr($joomla,0,3) >= '1.6'){
                $link = JRoute::_("index.php?option=com_users&view=login&return=".$redirectUrl, false);   
            }else{
                $link = JRoute::_("index.php?option=com_user&view=login&return=".$redirectUrl, false);    
            }
			JFactory::getApplication()->redirect($link, $msg);
		}
		
		$merchantId = JFactory::getSession()->get('merchantId');
		if($merchantId == null)
		{
			$merchant = JModel::getInstance('merchant','enmasseModel')->getByUserName(JFactory::getUser()->get('username'));
			if ($merchant != null)
				JFactory::getSession()->set('merchantId', $merchant->id);
			else
			{
	         	$msg = JText::_('MERCHANT_HAS_NO_ACCESS');
				$link = JRoute::_("index.php?option=com_enmasse&controller=deal&task=listing", false);
				JFactory::getApplication()->redirect($link, $msg);
			}
		}
		return $merchantId;
	}
}
?>
