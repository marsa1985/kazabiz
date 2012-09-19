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
jimport('joomla.application.component.controller');
JTable::addIncludePath('components'.DS.'com_enmasse'.DS.'tables');
require_once( JPATH_ADMINISTRATOR . DS ."components". DS ."com_enmasse".DS."helpers". DS ."EnmasseHelper.class.php");

class EnmasseControllerMerchant extends JController
{

	function display($cachable = false, $urlparams = false)
	{
		JRequest::setVar('view', 'merchant');
		JRequest::setVar('layout', 'show');
		parent::display();
	}
	function edit()
	{
		JRequest::setVar('view', 'merchant');
		JRequest::setVar('layout', 'edit');
		parent::display();
	}

	function add()
	{
		JRequest::setVar('view', 'merchant');
		JRequest::setVar('layout', 'edit');
		parent::display();
	}
	
	public function cancel()
	{
		JFactory::getApplication()->setUserState('merchant.add.data', null);
		$this->display();
		
	}
	
	
	function save()
	{
		$post = JRequest::get( 'post' );
		
		$data = array();
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
		if (!empty($branches)) {
			foreach($branches as $branch)
			{
				if(!empty($branch))
				{
					$final[$branch['branchname']] = $branch;
				}				
			}
		}
		
		$data['branches'] = json_encode($final);
		//check Joomla username was assigned for merchant
		$jUser = EnmasseHelper::getUserByName($data['user_name']);
		if(empty($jUser))
		{
			JFactory::getApplication()->setUserState('merchant.add.data', $data);
			$msg = JText::_('SAVE_ERROR_MSG') .": " .JText::_('MERCHANT_INVALID_USER_NAME');
			if($data['id'] > 0)
			{
				$link ='index.php?option=com_enmasse&controller=merchant&task=edit&cid[]=' .$data['id'];
			}else {
				
				$link = 'index.php?option=com_enmasse&controller=merchant&task=add';
			}
			
			JFactory::getApplication()->redirect($link, $msg, 'error');
		}
		
		//check duplicate username in merchant(branch) table 
		$model = JModel::getInstance('merchant','enmasseModel');
		
		$merchant = $model->checkUserNameDup($data['user_name'], $data['id']);
		
		if($merchant != null)
		{
			JFactory::getApplication()->setUserState('merchant.add.data', $data);
			$msg = JText::_('SAVE_ERROR_MSG') .": " .JText::_('DUP_MERCHANT_USERNAME_MSG') ."(".$merchant->name.")";
			
			if($data['id'] > 0)
			{
				$link = 'index.php?option=com_enmasse&controller=merchant&task=edit&cid[]=' .$data['id'];
			}else {
				$link = 'index.php?option=com_enmasse&controller=merchant&task=add';
			}
			
			JFactory::getApplication()->redirect($link, $msg, 'error');
			
		}
		else
		{
			if ($model->store($data))
			{
				//flush data was saved in the session
				JFactory::getApplication()->setUserState('merchant.add.data', null);
				$msg = JText::_('SAVE_SUCCESS_MSG');
				$this->setRedirect('index.php?option=com_enmasse&controller='.JRequest::getVar('controller'), $msg);
			}
			else
			{
				JFactory::getApplication()->setUserState('merchant.add.data', $data);
				$msg = JText::_('SAVE_ERROR_MSG') .": " . $model->getError();
				if($data['id'] > 0)
				{
					$link = 'index.php?option=com_enmasse&controller=merchant&task=edit&cid[]=' .$data['id'];
				}else {
					$link = 'index.php?option=com_enmasse&controller=merchant&task=add';
				}
					
				JFactory::getApplication()->redirect($link, $msg, 'error');
			}
		}
	}
	
	function control()
	{
		$this->setRedirect('index.php?option=com_enmasse');
	}

	function remove()
	{
		$cids = JRequest::getVar( 'cid', array(0), 'post', 'array' );
		
		$model = JModel::getInstance('merchant','enmasseModel');
	 	for($count=0; $count <count($cids); $count++)
		{
			$dealList = JModel::getInstance("deal","enmasseModel")->getDealByMerchantId($cids[$count]);
			if(count($dealList)!=0)
			{
				$merchant = $model->getById($cids[$count]);
				$wanring = $merchant->name.' ';
				$wanring.= JText::_("MERCHANT_IS_BEING_ASSIGNED_TO_A_DEAL");
				JFactory::getApplication()->redirect('index.php?option=com_enmasse&controller=merchant', $wanring , 'error');
			}
			
		}
		if($model->deleteList($cids))
		{
			$msg = JText::_('DELETE_SUCCESS_MSG');
			$this->setRedirect('index.php?option=com_enmasse&controller='.JRequest::getVar('controller'), $msg );
		}
		else
		{ 
			$msg = JText::_('DELETE_ERROR_MSG') .": " . $model->getError();
			$this->setRedirect('index.php?option=com_enmasse&controller='.JRequest::getVar('controller'), $msg, 'error');
		}
	}

	function publish()
	{
		EnmasseHelper::changePublishState(1,'enmasse_merchant_branch','merchant','merchant');
	}
	function unpublish()
	{
		EnmasseHelper::changePublishState(0,'enmasse_merchant_branch','merchant','merchant');
	}
	
	function checkValidUser()
	{
		$user = JRequest::getVar('username');
		echo EnmasseHelper::checkValidUser($user);
	}
	
    function checkDuplicatedName()
	{
		
		$merchantName = addslashes(JRequest::getVar("merchantName"));
		$merchantObj = JModel::getInstance('merchant','enmasseModel')->getMerchantByName($merchantName);
		if($merchantObj!=null)
	    	echo 'true';
	    else
	    	echo 'false';
		exit(0);
		
	}
    function checkUserName()
	{
		$userName = JRequest::getVar("userName");
		$user = EnmasseHelper::getUserByName($userName);
		$userByUserName = JModel::getInstance('merchant','enmasseModel')->getSaleByUserName($userName);
		if(!empty($user))
		{
			if(!empty($userByUserName))
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
}
?>
