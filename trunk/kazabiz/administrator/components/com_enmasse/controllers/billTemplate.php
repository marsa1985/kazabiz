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
require_once( JPATH_ADMINISTRATOR . DS ."components". DS ."com_enmasse".DS."helpers". DS ."BillHelper.class.php");

class EnmasseControllerBillTemplate extends JController
{

	function display($cachable = false, $urlparams = false)
	{
		JRequest::setVar('view', 'billtemplate');
		JRequest::setVar('layout', 'show');
		JRequest::setVar('task', 'show');
		parent::display();
	}
	function edit()
	{
		JRequest::setVar('view', 'billtemplate');
		JRequest::setVar('layout', 'edit');
		parent::display();
	}

	function save()
	{
		$data = JRequest::get( 'post' );
		
		$data['content'] 	= JRequest::getVar( 'content', '', 'post', 'string', JREQUEST_ALLOWRAW );
		
		$model = JModel::getInstance('billTemplate','enmasseModel');
		if ($model->store($data))
		{
			$msg = JText::_('SAVE_SUCCESS_MSG');
			$this->setRedirect('index.php?option=com_enmasse&controller='.JRequest::getVar('controller'), $msg);
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
	
	function preview()
	{
		$arOrder = JModel::getInstance("order", "EnmasseModel")->search();
		if(empty($arOrder))
		{
			echo "<h1>There haven't order for preview, please make a order before you can see bill preview<h1>";
			die;
		}else 
		{
			$nId = $arOrder[0]->id;			
		}
		BillHelper::createPDF($nId,'I');
		die;
	}
}
?>