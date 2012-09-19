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

class EnmasseControllerSalesPerson extends JController
{

	function display($cachable = false, $urlparams = false)
	{
		JRequest::setVar('view', 'salesperson');
		JRequest::setVar('layout', 'show');
		parent::display();
	}
	function edit()
	{
		JRequest::setVar('view', 'salesperson');
		JRequest::setVar('layout', 'edit');
		parent::display();
	}
	
	function selectSalesPerson()
	{
		JRequest::setVar('view', 'salesperson');
		JRequest::setVar('layout', 'selectsalesperson');
		parent::display();
	}
	
	function selectSalesPersonEdit()
	{
		JRequest::setVar('view', 'salesperson');
		JRequest::setVar('layout', 'selectsalespersonedit');
		parent::display();
	}

	function add()
	{
		JRequest::setVar('view', 'salesperson');
		JRequest::setVar('layout', 'edit');
		parent::display();
	}
	
	public function cancel()
	{
		JFactory::getApplication()->setUserState('salesperson.add.data', null);
		$this->display();
		
	}
	
	
	function save()
	{
		$data = JRequest::get( 'post' );

		//check Joomla username was assigned for merchant
		$jUser = EnmasseHelper::getUserByName($data['user_name']);
		if(empty($jUser))
		{
			JFactory::getApplication()->setUserState('salesperson.add.data', $data);
			$msg = JText::_('SAVE_ERROR_MSG') .": " .JText::_('SALE_PERSON_INVALID_USER_NAME');
			if($data['id'] > 0)
			{
				$link ='index.php?option=com_enmasse&controller=salesPerson&task=edit&cid[]=' .$data['id'];
			}else {
				$link = 'index.php?option=com_enmasse&controller=salesPerson&task=add';
			}
				
			JFactory::getApplication()->redirect($link, $msg, 'error');
		}
		$err = $this->validateSaleperson($data);
		//check duplicate salesperson username
		$model = JModel::getInstance('salesPerson','enmasseModel');
		$salesPerson = $model->checkUserNameDup($data['user_name'], $data['id']);
		
		$existName = $model->checkNameDup($data['name'], $data['id']);
		
		if(!empty($err))
		{
			$msg = JText::_('SAVE_ERROR_MSG') .": " .$err['msg'];
			if($data['id'] > 0)
			{
				$link ='index.php?option=com_enmasse&controller=salesPerson&task=edit&cid[]=' .$data['id'];
			}else {
				$link = 'index.php?option=com_enmasse&controller=salesPerson&task=add';
			}
			
			JFactory::getApplication()->redirect($link, $msg, 'error');
		}
		if($salesPerson != null)
		{
			JFactory::getApplication()->setUserState('salesperson.add.data', $data);
			$msg = JText::_('SAVE_ERROR_MSG') .": " .JText::_('DUP_SALES_PERSON_USERNAME_MSG') ."(".$salesPerson->name.")";
				
			if($data['id'] > 0)
			{
				$link ='index.php?option=com_enmasse&controller=salesPerson&task=edit&cid[]=' .$data['id'];
			}else {
				$link = 'index.php?option=com_enmasse&controller=salesPerson&task=add';
			}
				
			JFactory::getApplication()->redirect($link, $msg, 'error');
		}
		if($existName != null)
		{
			JFactory::getApplication()->setUserState('salesperson.add.data', $data);
			$msg = JText::_('SAVE_ERROR_MSG') .": " .JText::_('DUP_SALES_PERSON_NAME_MSG') ."(".$existName->name.")";
				
			if($data['id'] > 0)
			{
				$link ='index.php?option=com_enmasse&controller=salesPerson&task=edit&cid[]=' .$data['id'];
			}else {
				$link = 'index.php?option=com_enmasse&controller=salesPerson&task=add';
			}
				
			JFactory::getApplication()->redirect($link, $msg, 'error');
		}
		else//checking pass, begin save data
		{
			if ($model->store($data))
			{
				//flush data was saved in the session
				JFactory::getApplication()->setUserState('salesperson.add.data', null);
				$msg = JText::_('SAVE_SUCCESS_MSG');
				$this->setRedirect('index.php?option=com_enmasse&controller='.JRequest::getVar('controller'), $msg);
			}
			else
			{
				JFactory::getApplication()->setUserState('salesperson.add.data', $data);
				$msg = JText::_('SAVE_ERROR_MSG') .": " . $model->getError();
				
				if($data['id'] > 0)
				{
					$link ='index.php?option=com_enmasse&controller=salesPerson&task=edit&cid[]=' .$data['id'];
				}else {
					$link = 'index.php?option=com_enmasse&controller=salesPerson&task=add';
				}
					
				JFactory::getApplication()->redirect($link, $msg, 'error');
			}
		}

	}
	
	function control()
	{
		JFactory::getApplication()->setUserState('salesperson.add.data', null);
		
		$this->setRedirect('index.php?option=com_enmasse');
	}

	function remove()
	{
		$cids = JRequest::getVar( 'cid', array(0), 'post', 'array' );
		$arDel = array();
		$arWarning = array();
		$sWarningMsg = "";
		$model = JModel::getInstance('salesPerson','enmasseModel');

		for($count=0; $count <count($cids); $count++)
		{
			//check deal was assigned to this sales
			$salesPerson = $model->getById($cids[$count]);
			$dealList = JModel::getInstance("deal","enmasseModel")->getDealBySaleId($cids[$count]);
			if(count($dealList)> 0)
			{
				$arWarning[] = $cids[$count];
				$sWarningMsg .= $salesPerson->name.' ';
				$sWarningMsg.= JText::_("SALE_IS_BEING_ASSIGNED_TO_DEALS");
				$arDealId = array_keys($dealList);
				$sWarningMsg .= implode(', ', $arDealId) . ".<br>";
				//JFactory::getApplication()->redirect('index.php?option=com_enmasse&controller=salesPerson', $wanring , 'error');
			}else 
			{
				//check merchant was assigned to this sales
				$arMerchant = JModel::getInstance("merchant", "enmasseModel")->getMerchantBySaleId($cids[$count]);
				if(count($arMerchant) > 0)
				{
					$arWarning[] = $cids[$count];
					$sWarningMsg .= $salesPerson->name.' ';
					$sWarningMsg.= JText::_("SALE_IS_BEING_ASSIGNED_TO_MERCHANTS");
					$arMerchantId = array_keys($arMerchant);
					$sWarningMsg .= implode(', ', $arMerchantId) . ".<br>";
				}else
				{
					$arDel[] = $cids[$count];
				}
				
			}
		}
		
		if(count($arDel) > 0)
		{
			if($model->deleteList($arDel))
			{
				$msg = JText::sprintf('DELETE_SALES_PERSON_LIST_SUCCESS_MSG', implode(", ", $arDel));
				JFactory::getApplication()->enqueueMessage($msg);
			}
			else
			{
				$msg = JText::_('DELETE_ERROR_MSG') .": " . $model->getError();
				JFactory::getApplication()->enqueueMessage($msg, 'error');
			}
			
		}
		
		if(!empty($sWarningMsg))
		{
			JFactory::getApplication()->enqueueMessage($sWarningMsg, 'warning');
		}
		
		$this->setRedirect('index.php?option=com_enmasse&controller='.JRequest::getVar('controller'));
			
	}

	function publish()
	{
		EnmasseHelper::changePublishState(1,'enmasse_sales_person','salesPerson','salesPerson');
	}
	function unpublish()
	{
		$id = JRequest::getVar('changeId');
		$cid = JRequest::getVar('cid', array(), '', 'array');
		if ($id != null && !empty ($id)
		   && $cid != null && !empty($cid) )
		{
	        JArrayHelper::toInteger($cid);
	        
	        // change all deals to be assigned to new Sales Person
	        JModel::getInstance('deal','enmasseModel')->updateSalePerson($id, $cid);
	        // change all merchants to be assigned to new Sales Person
	        JModel::getInstance('merchant','enmasseModel')->updateSalePerson($id, $cid);
		}
        // unpublish old Sales Person(s)
		EnmasseHelper::changePublishState(0,'enmasse_sales_person','salesPerson','salesPerson');
	}
	
	function unpublishEdit()
	{
		$data = JRequest::get( 'post' );

		//check Joomla username was assigned for merchant
		$jUser = EnmasseHelper::getUserByName($data['user_name']);
		if(empty($jUser))
		{
			JFactory::getApplication()->setUserState('salesperson.add.data', $data);
			$msg = JText::_('SAVE_ERROR_MSG') .": " .JText::_('SALE_PERSON_INVALID_USER_NAME');
			if($data['id'] > 0)
			{
				$link ='index.php?option=com_enmasse&controller=salesPerson&task=edit&cid[]=' .$data['id'];
			}else {
				$link = 'index.php?option=com_enmasse&controller=salesPerson&task=add';
			}
				
			JFactory::getApplication()->redirect($link, $msg, 'error');
		}
		$err = $this->validateSaleperson($data);
		//check duplicate salesperson username
		$model = JModel::getInstance('salesPerson','enmasseModel');
		$salesPerson = $model->checkUserNameDup($data['user_name'], $data['id']);
		
		$existName = $model->checkNameDup($data['name'], $data['id']);
		
		if(!empty($err))
		{
			$msg = JText::_('SAVE_ERROR_MSG') .": " .$err['msg'];
			if($data['id'] > 0)
			{
				$link ='index.php?option=com_enmasse&controller=salesPerson&task=edit&cid[]=' .$data['id'];
			}else {
				$link = 'index.php?option=com_enmasse&controller=salesPerson&task=add';
			}
			
			JFactory::getApplication()->redirect($link, $msg, 'error');
		}
		if($salesPerson != null)
		{
			JFactory::getApplication()->setUserState('salesperson.add.data', $data);
			$msg = JText::_('SAVE_ERROR_MSG') .": " .JText::_('DUP_SALES_PERSON_USERNAME_MSG') ."(".$salesPerson->name.")";
				
			if($data['id'] > 0)
			{
				$link ='index.php?option=com_enmasse&controller=salesPerson&task=edit&cid[]=' .$data['id'];
			}else {
				$link = 'index.php?option=com_enmasse&controller=salesPerson&task=add';
			}
				
			JFactory::getApplication()->redirect($link, $msg, 'error');
		}
		if($existName != null)
		{
			JFactory::getApplication()->setUserState('salesperson.add.data', $data);
			$msg = JText::_('SAVE_ERROR_MSG') .": " .JText::_('DUP_SALES_PERSON_NAME_MSG') ."(".$existName->name.")";
				
			if($data['id'] > 0)
			{
				$link ='index.php?option=com_enmasse&controller=salesPerson&task=edit&cid[]=' .$data['id'];
			}else {
				$link = 'index.php?option=com_enmasse&controller=salesPerson&task=add';
			}
				
			JFactory::getApplication()->redirect($link, $msg, 'error');
		}
		else//checking pass, begin save data
		{
			if ($model->store($data))
			{
				//flush data was saved in the session
				JFactory::getApplication()->setUserState('salesperson.add.data', null);
				
				// update the unpublished
				$id = JRequest::getVar('changeId');
				$cid = JRequest::getVar('id', array(), '', 'array');
		        JArrayHelper::toInteger($cid);
		        
		        // change all deals to be assigned to new Sales Person
		        JModel::getInstance('deal','enmasseModel')->updateSalePerson($id, $cid);
		        // change all merchants to be assigned to new Sales Person
		        JModel::getInstance('merchant','enmasseModel')->updateSalePerson($id, $cid);
		        
		        // response with success message        
				$msg = JText::_('SAVE_SUCCESS_MSG');
				$this->setRedirect('index.php?option=com_enmasse&controller='.JRequest::getVar('controller'), $msg);
			}
			else
			{
				JFactory::getApplication()->setUserState('salesperson.add.data', $data);
				$msg = JText::_('SAVE_ERROR_MSG') .": " . $model->getError();
				
				if($data['id'] > 0)
				{
					$link ='index.php?option=com_enmasse&controller=salesPerson&task=edit&cid[]=' .$data['id'];
				}else {
					$link = 'index.php?option=com_enmasse&controller=salesPerson&task=add';
				}
					
				JFactory::getApplication()->redirect($link, $msg, 'error');
			}
		}
	}
	
	function checkDuplicatedName()
	{

		$saleName = addslashes(JRequest::getVar("saleName"));
		$saleObj = JModel::getInstance('salesperson','enmasseModel')->getSaleByName($saleName);
		if($saleObj!=null)
		echo 'true';
		else
		echo 'false';
		exit(0);

	}
	function checkUserName()
	{
		$userName = JRequest::getVar("userName");
		$user = EnmasseHelper::getUserByName($userName);
		$userByUserName = JModel::getInstance('salesperson','enmasseModel')->getSaleByUserName($userName);
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
	function  validateSaleperson($data = array())
	{
		$msg = null;
		$error = array();
		if(isset($data['name']) && ( strlen($data['name']) < 8 || strlen($data['name'])> 50))
		{
			$msg = JText::_('SALE_PERSON_INVALID_NAME_LENGH');
		}
		else if(isset($data['phone']) &&  $data['phone'] != "" &&  (strlen($data['phone']) < 8 || strlen($data['phone'])> 12))
		{
			$msg = JText::_('SALE_PERSON_INVALID_PHONE_LENGH');
		}
		else if(isset($data['address']) && $data['address'] != "" && (strlen($data['address']) < 10 || strlen($data['address']) > 100))
		{
			$msg = JText::_('SALE_PERSON_INVALID_ADDRESS_LENGH');
		}
		else if(isset($data['email']) &&  $data['email'] != "" &&  (strlen($data['email']) < 10 || strlen($data['email'])> 50))
		{
			$msg = JText::_('SALE_PERSON_INVALID_EMAIL_LENGH');
		}
		if($msg != null)
		{
			$error['msg'] = $msg;
		}
		return $error;
	}
	
	function ajaxHasDealsOrMerchant() {
		$cid = JRequest::getVar('cid', array(), '', 'array');
        JArrayHelper::toInteger($cid);
        
		// get count deal from by SalePerson list
		$dealCount = JModel::getInstance('deal','enmasseModel')->getCountBySalePersonList($cid);
        // get count merchant from by SalePerson list
        $merchantCount = JModel::getInstance('merchant','enmasseModel')->getCountBySalePersonList($cid);
        
        if ($dealCount + $merchantCount > 0) {
        	echo "true";
        } else {
        	echo "false";
        }
        exit(0);
	}
}
?>