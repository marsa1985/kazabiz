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

defined( '_JEXEC' ) or die( 'Restricted access' );
jimport( 'joomla.database.database' );
jimport('joomla.application.component.controller');
JTable::addIncludePath('components'.DS.'com_enmasse'.DS.'tables');
require_once( JPATH_ADMINISTRATOR . DS ."components". DS ."com_enmasse".DS."helpers". DS ."EnmasseHelper.class.php");

class EnmasseControllerDeal extends JController
{  
	function display($cachable = false, $urlparams = false)
	{
		JRequest::setVar('view', 'deal');
		JRequest::setVar('layout', 'show');
		parent::display();
	}

	function edit()
	{
		JRequest::setVar('view', 'deal');
		JRequest::setVar('layout', 'edit');
		parent::display();
	}

	function add()
	{
		JRequest::setVar('view', 'deal');
		JRequest::setVar('layout', 'edit');
		parent::display();
	}

	function save()
	{
		$data = JRequest::get( 'post' );
		$checkchracter = EnmasseHelper::checkSpecialCharacter($data['name']);
		
		if($checkchracter)
		{
			$msg =JText::_('SAVE_DEAL_NOTICE');
			if($data['id'] == null)
			{
				$this->setRedirect('index.php?option=com_enmasse&controller='.JRequest::getVar('controller').'&task=add', $msg, 'notice');
				return false;
			}
			else 
			{
				$this->setRedirect('index.php?option=com_enmasse&controller='.JRequest::getVar('controller').'&task=edit&cid[0]='. $data['id'], $msg, 'notice');
				return false;
			}
		}
			
		$data['slug_name'] 		= EnmasseHelper::seoUrl($data['name']);
		$data['description'] 	= JRequest::getVar( 'description', '', 'post', 'string', JREQUEST_ALLOWRAW ); 
		$data['highlight'] 		= JRequest::getVar( 'highlight', '', 'post', 'string', JREQUEST_ALLOWRAW ); 
		$data['terms'] 			= JRequest::getVar( 'terms', '', 'post', 'string', JREQUEST_ALLOWRAW ); 
		
        if($data['slug_name']=='_' || $data['slug_name'] =='')
        {
           $now = str_replace(":"," ",DatetimeWrapper::getDatetimeOfNow());
           $data['slug_name'] = EnmasseHelper::seoUrl($now);
        }
        
		$model = JModel::getInstance('deal','enmasseModel');
		
		//---------------------------------------------------------------
		// if edit deal
		if($data['id'] > 0)
		{
			//---get deal data
			$deal = JModel::getInstance('deal','enmasseModel')->getById($data['id']);
			
			// get sold coupon qty for deal
			$soldCouponList = JModel::getInstance('invty','enmasseModel')->getSoldCouponByPdtId($deal->id);
			
		    //if from unlimited to limited
			if($deal->max_coupon_qty < 0  ) 
			{
				if($data['max_coupon_qty'] > 0)
				{
				    if($data['max_coupon_qty'] <= count($soldCouponList) )
					{
						$msg = JText::_('MSG_CURRENT_SOLD_GRATER_THAN_MODIFIED_COUPON');
						JFactory::getApplication()->redirect('index.php?option=com_enmasse&controller=deal&task=edit&cid='.$data['id'],$msg);
					}
					else
					{
						$numOfAddCoupon  = $data['max_coupon_qty']- count($soldCouponList);
					}
				}
			}
			else 
			{   //---------------- if change from limited to unlimited
				if($data['max_coupon_qty'] < 0 )
				 	$unlimit = true;
				 //-------------------------change the coupon qty to less
				else if($data['max_coupon_qty'] < $deal->max_coupon_qty)
				{
					//---------------------- if new coupon qty <= the sold coupon qty
					if( $data['max_coupon_qty'] <= count($soldCouponList) )
					{
						$msg = JText::_('MSG_CURRENT_SOLD_GRATER_THAN_MODIFIED_COUPON');
						JFactory::getApplication()->redirect('index.php?option=com_enmasse&controller=deal&task=edit&cid='.$data['id'],$msg);
					}
					else
					{
						$numOfRemoveCoupon = $deal->max_coupon_qty -  $data['max_coupon_qty'];
					}
				} //------------------ if change to coupon qty to greater qty
				else if($data['max_coupon_qty'] > $deal->max_coupon_qty)
					$numOfAddCoupon = $data['max_coupon_qty'] - $deal->max_coupon_qty;
			}
			// suong.mai edit save description to oder_item
			$db =   & JFactory::getDBO();
			$query = "UPDATE #__enmasse_order_item
					  SET description ='".$data['name']."'"
					."WHERE pdt_id=".$data['id'];
			$db->setQuery($query)	;		
			$db->query();
			//$db->updateObject($this->, $object, $key)
			
			
		}
		
			//------------------------
		//gemerate integration class
		 $integrateFileName = EnmasseHelper::getSubscriptionClassFromSetting().'.class.php';
		 $integrationClass = EnmasseHelper::getSubscriptionClassFromSetting();
		 require_once (JPATH_SITE . DS ."components". DS ."com_enmasse". DS ."helpers". DS ."subscription". DS .$integrationClass. DS.$integrateFileName);
		 $integrationObject = new $integrationClass();
		 
		// store data
		$row = $model->store($data);
		
		if ($row->success)
		{ 
			if($data['id'] == 0)
				$integrationObject->integration($row,'newDeal');
			//--------------------------------------
			// store location and category
			JModel::getInstance('dealCategory','enmasseModel')->store($row->id,$data['pdt_cat_id']);
			JModel::getInstance('dealLocation','enmasseModel')->store($row->id,$data['location_id']);
			
			// if is new deal and limited the coupdon then create coupon in invty
			if($data['id']==0 && $row->max_coupon_qty > 0)
			{
				for($i=0 ; $i < $row->max_coupon_qty; $i++)
				{
					
					$name = $i+1;
					JModel::getInstance('invty','enmasseModel')->generateCouponFreeStatus($row->id,$name,'Free');
				}
				
			}
			else if($data['id']!=0)
			{
				if(!empty($numOfRemoveCoupon))
				{
					$freeCouponList = JModel::getInstance('invty','enmasseModel')->getCouponFreeByPdtID($data['id']);
					// removed the coupons from invty
					for($i=0; $i < $numOfRemoveCoupon ; $i++)
					{
						JModel::getInstance('invty','enmasseModel')->removeById($freeCouponList[$i]->id);
					}
				}
				else if(!empty($numOfAddCoupon))
				{
					// add more coupon to invty
					for($i=0; $i < $numOfAddCoupon ; $i++)
					{
						$name = $i+1;
						JModel::getInstance('invty','enmasseModel')->generateCouponFreeStatus($data['id'],$name,'Free');
					}
				}
				else if($unlimit)
				{
					//remove all free coupon 
					JModel::getInstance('invty','enmasseModel')->removeCouponByPdtIdAndStatus($data['id'],'Free');
					
				}
				
			}
			
			$msg = JText::_('SAVE_SUCCESS_MSG');
			$this->setRedirect('index.php?option=com_enmasse&controller='.JRequest::getVar('controller'), $msg,$type);
		}
		else
		{
			$msg = JText::_('SAVE_ERROR_MSG') .": " . $model->getError();
			if($data['id'] == null)
				$this->setRedirect('index.php?option=com_enmasse&controller='.JRequest::getVar('controller').'&task=add', $msg, 'error');
			else
				$this->setRedirect('index.php?option=com_enmasse&controller='.JRequest::getVar('controller').'&task=edit&cid[0]='. $data['id'], $msg, 'error');
		}
	}

	function control()
	{
		$this->setRedirect('index.php?option=com_enmasse');
	}

	function remove()
	{
		$cids = JRequest::getVar( 'cid', array(0), 'post', 'array' );

		$model = JModel::getInstance('deal','enmasseModel');		
		
		//check whether we can delete the deal or not (can be delete the deal that have order on it)
		$arCannotDel = JModel::getInstance('orderItem','enmasseModel')->getDealIdCannotDelete();
		$arDel = array_diff($cids, $arCannotDel);
		if(count($cids) > count($arDel))
		{
			$warningMsg = JText::sprintf("DEAL_CANNOT_DELETE_MSG", implode(', ', array_diff($cids, $arDel)));
			JFactory::getApplication()->enqueueMessage($warningMsg, 'warning');
		}
		
		if(count($arDel) > 0)
		{
			if($model->deleteList($arDel))
			{
				$this->refreshOrder();
				$msg = JText::sprintf('DEAL_DELETE_SUCCESS_MSG', implode(', ', $arDel));
				$this->setRedirect('index.php?option=com_enmasse&controller='.JRequest::getVar('controller'), $msg );
			}
			else
			{
				$msg = JText::_('DELETE_ERROR_MSG') .": " . $model->getError();
				$this->setRedirect('index.php?option=com_enmasse&controller='.JRequest::getVar('controller'), $msg, 'error');
			}
		}
		
		$this->setRedirect('index.php?option=com_enmasse&controller='.JRequest::getVar('controller'));
	}

	function publish()
	{
		EnmasseHelper::changePublishState(1,'enmasse_deal','deal','deal');
	}
	function unpublish()
	{
		EnmasseHelper::changePublishState(0,'enmasse_deal','deal','deal');
	}

	function approveDeal()
	{
		$db = JFactory::getDBO();
		$option ='com_enmasse';
		$cid = JRequest::getVar( 'cid', array(), '', 'array' );
		$application = JFactory::getApplication();
		
		if($cid == null || count($cid)==0)
		{
			$application ->redirect('index.php?option='.$option.'&controller=deal', JText::_('CHOOSE_DEAL_TO_APPROVE'),'error');
		}
		
		for ($i=0; $i < count($cid); $i++)
		{
			$status = JModel::getInstance('deal','enmasseModel')->getStatus($cid[$i]);
			if($status != "Pending")
			{
				$msg = JText::_('CHOOSE_PENDING_DEAL_TO_CONFIRM');
				$application ->redirect('index.php?option=' . $option.'&controller=deal', $msg,'error');
			}
		}

		for ($i=0; $i < count($cid); $i++)
			$status = JModel::getInstance('deal','enmasseModel')->updateStatus($cid[$i], "On Sales");
		
		$application->redirect('index.php?option='.$option.'&controller=deal', JText::_('APPROVE_DEAL_SUCCESS_MSG'));
	}
	
	function voidDeal()
	{
		$db = JFactory::getDBO();
		$option = 'com_enmasse';
		$cid = JRequest::getVar( 'cid', array(), '', 'array' );
		$appication = JFactory::getApplication();
		
		if($cid == null)
			$appication->redirect('index.php?option=' . $option.'&controller=deal', JText::_('CHOOSE_DEAL_TO_VOID'),'error');

		for ($i=0; $i < count($cid); $i++)
		{
			$status = JModel::getInstance('deal','enmasseModel')->getStatus($cid[$i]);

			if($status == "Voided")
				$appication ->redirect('index.php?option=' . $option.'&controller=deal', JText::_('CANNOT_VOID_VOIDED_DEAL_MSG'),'error');
			elseif($status == "Confirmed")
				$appication ->redirect('index.php?option=' . $option.'&controller=deal', JText::_('CANNOT_VOID_CONFIRMED_DEAL'),'error');
			elseif($status == "Pending")
				$appication ->redirect('index.php?option=' . $option.'&controller=deal', JText::_('CANNOT_VOID_PENDING_DEAL'),'error');
				
		}		


		for ($i=0; $i < count($cid); $i++)
		{
			$dealId = $cid[$i];

			$orderItemList = JModel::getInstance('orderItem','enmasseModel')->listByPdtIdAndStatus($dealId, "Paid");
			
			foreach($orderItemList as $orderItem)
			{
				EnmasseHelper::orderItemWaitingForRefund($orderItem);
				sleep(1);
			}
			// update status
			JModel::getInstance('deal','enmasseModel')->updateStatus($dealId , 'Voided');
			//Integration with point system, refund point automatically if users paid with point
			if(EnmasseHelper::isPointSystemEnabled())
			{
				//generate integration class
				$integrationClass = EnmasseHelper::getPointSystemClassFromSetting();
				$integrateFileName = $integrationClass.'.class.php';			
				require_once (JPATH_SITE . DS ."components". DS ."com_enmasse". DS ."helpers". DS ."pointsystem". DS .$integrationClass. DS.$integrateFileName);		
				$buyerList = EnmasseHelper::getBuyerRefundVoidDeal($dealId);
				$integrationObject = new $integrationClass();		
				foreach($buyerList as $buyer)
				{
					$integrationObject->integration($buyer['buyer_id'],'refunddeal',$buyer['point_used_to_pay']);
				}
			}			
		}
		$appication->redirect('index.php?option='.$option.'&controller=deal', JText::_('VOID_DEAL_SUCCESS_MSG'));
	}


	function confirmDeal()
	{
		$db = JFactory::getDBO();
		$option ='com_enmasse';
		$cid = JRequest::getVar( 'cid', array(), '', 'array' );
		$name = "";
		$appication = JFactory::getApplication();
		
		if($cid == null)
			$appication->redirect('index.php?option='.$option.'&controller=deal', JText::_('CHOOSE_DEAL_TO_CONFIRM'),'error');

		for ($i=0; $i < count($cid); $i++)
		{
			$status = JModel::getInstance('deal','enmasseModel')->getStatus($cid[$i]);

			if($status == "Confirmed")
				$appication ->redirect('index.php?option=' . $option.'&controller=deal', JText::_('CANNOT_CONFIM_DEAL_CONFIRMED'),'error');
			elseif($status == "Voided")
				$appication ->redirect('index.php?option=' . $option.'&controller=deal', JText::_('CANNOT_CONFIM_DEAL_VOIDED'),'error');
			elseif($status == "Pending")
				$appication ->redirect('index.php?option=' . $option.'&controller=deal', JText::_('CANNOT_CONFIM_DEAL_PENDING'),'error');

		}
		
			//------------------------
			//generate integration class			
			$isPointSystemEnabled = EnmasseHelper::isPointSystemEnabled();
			if($isPointSystemEnabled==true)
			{
				$integrationClass = EnmasseHelper::getPointSystemClassFromSetting();
				$integrateFileName = $integrationClass.'.class.php';			
				require_once (JPATH_SITE . DS ."components". DS ."com_enmasse". DS ."helpers". DS ."pointsystem". DS .$integrationClass. DS.$integrateFileName);
			}
					
		for ($i=0 ; $i < count($cid); $i++)
		{
			$dealId = $cid[$i];

			$orderItemList = JModel::getInstance('orderItem','enmasseModel')->listByPdtIdAndStatus($dealId, "Paid");
			
			foreach($orderItemList as $orderItem)
			{
				EnmasseHelper::orderItemDelivered($orderItem);
								
				//------------------------
				//generate integration class
				if($isPointSystemEnabled==true)
				{	
					$buyerId = EnmasseHelper::getUserIdByOrderId($orderItem->order_id);
					$referralId = EnmasseHelper::getReferralIdByOrderId($orderItem->order_id);			
					$integrationObject = new $integrationClass();				
					$integrationObject->integration($buyerId,'confirmdeal');
					
					if(!empty($referralId))
					{
						$integrationObject->integration($referralId,'referralbonus');
					}
				}
					
				sleep(1);
			}
			JModel::getInstance('deal','enmasseModel')->updateStatus($dealId, 'Confirmed');
			
			if($isPointSystemEnabled==true) //Refund point to who paid with point but without paying by cash
			{
				$buyerList = EnmasseHelper::getBuyerRefundConfirmDeal($dealId);
				$integrationObject = new $integrationClass();		
				foreach($buyerList as $buyer)
				{
					$integrationObject->integration($buyer['buyer_id'],'refunddeal',$buyer['point_used_to_pay']);
				}
			}				
		}
		$appication-> redirect('index.php?option=' . $option.'&controller=deal', JText::_('CONFIRM_DEAL_SUCCESS_MSG'));
	}

	function refreshOrder($by=null)
	{
		JModel::getInstance('deal','enmasseModel')->refreshOrder($by=null);
	}
	function upPosition()
	{
		EnmasseControllerDeal::changePosition('com_enmasse', true);
	}
	function downPosition()
	{
		EnmasseControllerDeal::changePosition('com_enmasse', false);
	}
	function changePosition($option, $up)
	{
		// get current item
		$id = JRequest::getVar('id');
		$cur = JModel::getInstance('deal','enmasseModel')->getCurrentPosition($id);
		
		// get other item
		if ($up)
			$temp = $cur->position - 1;
		else
			$temp = $cur->position + 1;
		$other = JModel::getInstance('deal','enmasseModel')->getNextPosition($temp);

		// change position
		if ($up)
		{
			$cur->position --;
			$other->position ++;
		}
		else
		{
			$cur->position ++;
			$other->position --;
		}

		JModel::getInstance('deal','enmasseModel')->store($cur);
		if ($other->id)
			JModel::getInstance('deal','enmasseModel')->store($other);

		// redirect
		$msg = JText::_( "ORDER_POSITION_UPDATED");
		JFactory::getApplication()->redirect("index.php?option=com_enmasse&controller=deal", $msg);

	}
	
	function updatePosition()
	{
		$id = JRequest::getVar('id');
		$updatePosition = JRequest::getVar('updatePosition');
		$listPosition = JModel::getInstance('deal','enmasseModel')->getListPosition($id);
		$cur = JModel::getInstance('deal','enmasseModel')->getCurrentPosition($id);
		$other = JModel::getInstance('deal','enmasseModel')->getOtherPosition($id);
       
		// Check value of the position was updated, max value of it shouldn`t greater than total deals
		$nTotal = count($other) + 1;
		if($updatePosition >$nTotal) $updatePosition = $nTotal;
		
		if($updatePosition > $cur->position)
		{
			for($i=0; $i<count($other);$i++)
			{
				if($other[$i]->position <= $updatePosition && $other[$i]->position > $cur->position)
				{
					$other[$i]->position -=1;
					JModel::getInstance('deal','enmasseModel')->store($other[$i]);
				}
			}
		}elseif ($updatePosition < $cur->position)
		{
			for($z=0; $z<count($other);$z++)
			{
			if($other[$z]->position >= $updatePosition && $other[$z]->position < $cur->position)
				{
					$other[$z]->position +=1;
					JModel::getInstance('deal','enmasseModel')->store($other[$z]);
				}
			}
		}
		
		$cur->position = $updatePosition;
		JModel::getInstance('deal','enmasseModel')->store($cur);
		
		// redirect
		$msg = JText::_( "ORDER_POSITION_UPDATED");
		JFactory::getApplication()->redirect("index.php?option=com_enmasse&controller=deal", $msg);
	}

    function checkDuplicatedDeal()
	{
		
		$dealName = addslashes(JRequest::getVar("dealName"));
		$dealObj = JModel::getInstance('deal','enmasseModel')->getDealByName($dealName);
		if($dealObj != null)
		 echo 'true';
		else
		 echo 'false';
		die;
		
	}
}
?>