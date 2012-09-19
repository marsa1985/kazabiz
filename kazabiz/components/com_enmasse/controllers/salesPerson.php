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
JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_enmasse'.DS.'tables');
require_once( JPATH_ADMINISTRATOR . DS ."components". DS ."com_enmasse". DS ."helpers". DS ."EnmasseHelper.class.php");

class EnmasseControllerSalesPerson extends JController
{		
	function dealShow()
	{
		$this->checkAccess();
		
		JRequest::setVar('view', 'salesperson');
		JRequest::setVar('task', 'dealShow');
		parent::display();
	}
	
	function dealEdit()
	{
		$dAuthor = $this->checkAccess();
		$cid 	= JRequest::getVar( 'cid', array(0), '', 'array' );
		$row = JModel::getInstance('deal','enmasseModel')->getById($cid[0], $dAuthor);
		
		if( !$row )
		{
			$msg = JText::_('SALES_PERSON_NO_PERMISION_ON_DEAL');
			JFactory::getApplication()->redirect(JRoute::_('index.php?option=com_enmasse&controller=salesPerson&task=dealShow'), $msg);		
		}
		// when deal is approved, it's status is at "On Sales", it cannot be edit from front-end.
		else if($row->status == "On Sales")
		{
			JFactory::getApplication()->enqueueMessage(JText::_("SALES_PERSON_CANNOT_EDIT_DEAL_MSG"), 'notice');
			JRequest::setVar('editable', false);
		} // not editable since it's already pass the sate of on Sale
		else if($row->status != "Pending" && strtotime($row->start_at) < time())
		{
			JFactory::getApplication()->enqueueMessage(JText::_("SALES_PERSON_CANNOT_EDIT_NOT_PENDING_DEAL_MSG"), 'notice');
			JRequest::setVar('editable', false);
		}
		JRequest::setVar('view', 'salesperson');
		JRequest::setVar('task', 'dealEdit');
		parent::display();
	}
	
	function dealAdd()
	{
		$this->checkAccess();
		
		JRequest::setVar('view', 'salesperson');
		JRequest::setVar('task', 'dealEdit');
		parent::display();
	}

	function dealSave()
	{
		$dAuthorId = $this->checkAccess();
		
		$data = JRequest::get( 'post' );
		
		$data['slug_name'] 		= EnmasseHelper::seoUrl($data['name']);
		$data['description'] 	= JRequest::getVar( 'description', '', 'post', 'string', JREQUEST_ALLOWRAW ); 
		$data['highlight'] 		= JRequest::getVar( 'highlight', '', 'post', 'string', JREQUEST_ALLOWRAW ); 
		$data['terms'] 			= JRequest::getVar( 'terms', '', 'post', 'string', JREQUEST_ALLOWRAW ); 
	    // if is edit
		if($data['id']!=0)
		{
			$deal = JModel::getInstance('deal','enmasseModel')->getById($data['id'], $dAuthorId);
			if(!deal || ($deal->status == "On Sales" && strtotime($deal->start_at) < time()))
			{
				$msg = JText::_('SALES_PERSON_EDIT_DEAL_ON_SALE_ERROR');
				JFactory::getApplication()->redirect(JRoute::_('index.php?option=com_enmasse&controller=salesPerson&task=dealShow'), $msg, 'error');		
			}
			if($data['max_coupon_qty'] > 0 && $data['max_coupon_qty'] < $deal->max_coupon_qty )
			{
				if( $data['max_coupon_qty'] <= $deal->cur_sold_qty  )
				{
					$msg = JText::_('MSG_CURRENT_SOLD_GRATER_THAN_MODIFIED_COUPON');
					JFactory::getApplication()->redirect('index.php?option=com_enmasse&controller=deal&task=edit&cid='.$data['id'],$msg);
				}
				else
				{
					$removeCoupons = $deal->max_coupon_qty -  $data['max_coupon_qty'];
				}
			}
			else if($data['max_coupon_qty'] > 0 && $data['max_coupon_qty'] > $deal->max_coupon_qty)
			{
				if($deal->max_coupon_qty < 0)
					$addCoupons = $data['max_coupon_qty'];
				else
					$addCoupons = $data['max_coupon_qty'] - $deal->max_coupon_qty;
			}
			else if($data['max_coupon_qty'] < 0 && $deal->max_coupon_qty > 0)
			{
				$unlimit = true;
			}
		}
		
		$data['status'] 			= "Pending";//set satus of this deal to Pending (for modify and add new)
		$data['sales_person_id'] 	= JFactory::getSession()->get('salesPersonId');
        $row = JModel::getInstance('deal','enmasseModel')->store($data);
		if ($row->success) 
		{
			
			// add location and category
			JModel::getInstance('dealCategory','enmasseModel')->store($row->id,$data['pdt_cat_id']);
			JModel::getInstance('dealLocation','enmasseModel')->store($row->id,$data['location_id']);
		// if is not edit and limited the coupon
			if($data['id']==0 && $row->max_coupon_qty > 0)
			{
				for($i=0 ; $i< $row->max_coupon_qty;$i++)
				{
					$name = $i+1;
					JModel::getInstance('invty','enmasseModel')->generateCouponFreeStatus($row->id,$name,'Free');
				}
			}
			else if($data['id']!=0)
			{
				if(!empty($removeCoupons))
				{
					$freeCoupon = JModel::getInstance('invty','enmasseModel')->getCouponFreeByPdtID($data['id']);
					for($i=0; $i < $removeCoupons ; $i++)
					{
						JModel::getInstance('invty','enmasseModel')->removeById($freeCoupon[$i]->id);
					}
				}
				else if(!empty($addCoupons))
				{
					for($i=0; $i < $addCoupons ; $i++)
					{
						$name = $i+1;
						JModel::getInstance('invty','enmasseModel')->generateCouponFreeStatus($data['id'],$name,'Free');
					}
				}
				else if($unlimit)
				{
					JModel::getInstance('invty','enmasseModel')->removeCouponByPdtIdAndStatus($data['id'],'Free');
				}
				
			}
			$msg = JText::_( 'DEAL_PENDING_MSG' );
		}
		else
			$msg = JText::_( 'DEAL_SAVE_ERROR' );

		// Check the table in so it can be edited.... we are done with it anyway
		$link = 'index.php?option=com_enmasse&controller=salesPerson&task=dealShow';
		$this->setRedirect($link, $msg);
	}
	
	public function merchantList()
	{
		$dAuthor = $this->checkAccess();
		JRequest::setVar('view', 'salesperson');
		JRequest::setVar('task', 'merchantList');
		parent::display();		
	}
	
	public function merchantEdit()
	{
		$dAuthor = $this->checkAccess();
		JRequest::setVar('view', 'salesperson');
		JRequest::setVar('task', 'merchantEdit');
		parent::display();	
	}
	
	public function merchantSave()
	{
		$dAuthor = $this->checkAccess();
		$post = JRequest::get( 'post' );
		
		$data = array();
		
		//------- extract merchant branch information
		for($i=1; $i<=$post['num_of_branches'];$i++)
		{
			$branches["branch" . $i] = array();
		}
		$removeId = 0;
		foreach ($post as $key=>$value)
		{
			$temp = explode("-", $key);
			if(count($temp)==2)
			{
				if($temp[0]=="remove")
				{
					$removeId = $temp[1];
				}
				if($temp[1]!=$removeId)
				{
					$branches["branch" . $temp[1]][$temp[0]] = $value;
				}
			}
			else
			{
				$data[$key] = $value;
			}
		}		
		
		$final = array();
		
		foreach($branches as $branch)
		{
			if(!empty($branch))
			{
				$final[$branch['branchname']] = $branch;
			}				
		}
		
		$data['branches'] = json_encode($final);
		
		$jUser = EnmasseHelper::getUserByName($data['user_name']);
		if(empty($jUser))
		{
			JFactory::getApplication()->setUserState('saleperson.merchant_add.data', $data);
			$msg = JText::_('SAVE_ERROR_MSG') .": " .JText::_('MERCHANT_INVALID_USER_NAME');
			
			if($data['id'] == null)
				$link =  JRoute::_('index.php?option=com_enmasse&controller=salesPerson&task=merchantEdit', false);
			else
				$link = JRoute::_('index.php?option=com_enmasse&controller=salesPerson&task=merchantEdit&cid[0]='. $data['id']);
			
			JFactory::getApplication()->redirect($link, $msg, 'error');
		}
		
		//check duplicate username in merchant(branch) table 
		$oModel = JModel::getInstance('merchant', 'EnmasseModel');
		
		$merchant = $oModel->checkUserNameDup($data['user_name'], $data['id']);
		
		if($merchant != null)
		{
			JFactory::getApplication()->setUserState('saleperson.merchant_add.data', $data);
			$msg = JText::_('SAVE_ERROR_MSG') .": " .JText::_('DUP_MERCHANT_USERNAME_MSG') ."(".$merchant->name.")";
			
			if($data['id'] == null)
				$link = JRoute::_('index.php?option=com_enmasse&controller=salesPerson&task=merchantEdit', false);
			else
				$link = JRoute::_('index.php?option=com_enmasse&controller=salesPerson&task=merchantEdit&cid[0]='. $data['id'], false);
			
			JFactory::getApplication()->redirect($link, $msg, 'error');
			
		}
		
		//set current logged in user is sales person of the create merchant
		$data['sales_person_id'] = $dAuthor;
		
		//--------------save data
		
		if ($oModel->store($data))
		{
			$msg = JText::_('SALE_PERSON_MERCHANT_SAVE_SUCCESS_MSG');
			$this->setRedirect(JRoute::_('index.php?option=com_enmasse&controller=salesPerson&task=merchantList', false), $msg);
		}
		else
		{
			$msg = JText::_('SALE_PERSON_MERCHANT_SAVE_FAIL_MSG') .": " . $oModel->getError();
			if(empty($data['id']))
				$this->setRedirect(JRoute::_('index.php?option=com_enmasse&controller=salesPerson&task=merchantEdit', false), $msg, 'error');
			else
				$this->setRedirect(JRoute::_('index.php?option=com_enmasse&controller=salesPerson&task=merchantEdit&cid[0]='. $data['id'], false), $msg, 'error');
		}
	}
	
	public function checkUserName()
	{
		$userName = JRequest::getVar("userName");
		$nMerId   = JRequest::getVar("mer_id", null);
		$user = EnmasseHelper::getUserByName($userName);
		$merchant = JModel::getInstance('merchant','enmasseModel')->checkUserNameDup($userName, $nMerId);
		
		if(!empty($user))
		{
			if(!empty($merchant))
			 	echo 'duplicated';
			else
		 		echo 'valid';
		}
		else
		{
			echo 'invalid';
		}
		exit(0);
	}
	
	public function checkMerchantName()
	{
		$merchantName = addslashes(JRequest::getVar("merchantName"));
		$merchantObj = JModel::getInstance('merchant','enmasseModel')->getMerchantByName($merchantName);
		if($merchantObj!=null)
	    	echo 'true';
	    else
	    	echo 'false';
		exit(0);
	}
	
//---------------------------------------------
	
	private function checkAccess()
	{
	    if (JFactory::getUser()->get('guest'))
		{			
			$msg = JText::_( "SALE_PLEASE_LOGIN_BEFORE");
			$redirectUrl = base64_encode("index.php?option=com_enmasse&controller=salesPerson&task=dealShow");  
		    $version = new JVersion;
            $joomla = $version->getShortVersion();
            if(substr($joomla,0,3) >= '1.6'){
                $link = JRoute::_("index.php?option=com_users&view=login&return=".$redirectUrl, false);  
            }else{
                $link = JRoute::_("index.php?option=com_user&view=login&return=".$redirectUrl, false);    
            }
			JFactory::getApplication()->redirect($link, $msg);
		}
		
		$salesPersonId = JFactory::getSession()->get('salesPersonId');

		if($salesPersonId == null)
		{
			$salesPerson = JModel::getInstance('salesPerson','enmasseModel')->getByUserName(JFactory::getUser()->get('username'));
			if ($salesPerson != null)
				JFactory::getSession()->set('salesPersonId', $salesPerson->id);
			else
			{
	         	$msg = JText::_('SALE_NO_ACCESS');
				$link = JRoute::_("index.php?option=com_enmasse&controller=deal&task=listing", false);
				JFactory::getApplication()->redirect($link, $msg);
			}
		}
		return $salesPersonId;
	}
	
	
}
?>
