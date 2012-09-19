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

class EnmasseControllerSetting extends JController
{

	function display($cachable = false, $urlparams = false)
	{
		JRequest::setVar('view', 'setting');
		JRequest::setVar('layout', 'edit');
		parent::display();
	}
	function save()
	{
		$data = JRequest::get( 'post' );
        
        $version = new JVersion;
        $joomla = $version->getShortVersion();
        $err = $this->validateSetting($data);
         if(substr($joomla,0,3) >= '1.6'){        	      
            $data['article_id'] = JRequest::getVar('_id');                
        }
        else
        { 
            $control_name = JRequest::getVar('control_name');
            $data['article_id'] = $control_name['name'];
        }              
		
		$model = JModel::getInstance('setting','enmasseModel');
		
		if(! empty($err))
		{
			$msg = JText::_('SAVE_ERROR_MSG') .": " . $err['msg'];
			if($data['id'] == null)
				$this->setRedirect('index.php?option=com_enmasse&controller='.JRequest::getVar('controller').'&task=add', $msg, 'error');
			else
			{
				$this->setRedirect('index.php?option=com_enmasse&controller='.JRequest::getVar('controller').'&task=edit&cid[0]='. $data['id'], $msg, 'error');
			}
		}
		else if ($model->store($data))
		{
			$msg = JText::_('SAVE_SUCCESS_MSG');
			$this->setRedirect('index.php?option=com_enmasse', $msg);
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
	function validateSetting($data = null)
	{
		
			$msg = null;
			$error = array();
			if(isset($data['company_name']) && ( strlen($data['company_name']) < 8 || strlen($data['company_name'])> 50))
			{
				$msg = JText::_('SETTING_INVALID_NAME_LENGH');
			}
			else if(isset($data['address1']) && (strlen($data['address1']) < 8 || strlen($data['address1'])> 255))
			{
				$msg = JText::_('SETTING_INVALID_ADDRESS1_LENGH');
			}
			else if(isset($data['address2']) && $data['address2'] != "" && (strlen($data['address2']) > 255))
			{
				$msg = JText::_('SETTING_INVALID_ADDRESS2_LENGH');
			}
			else if(isset($data['city']) &&  $data['city'] != "" &&  (strlen($data['city']) < 3 || strlen($data['city'])> 50))
			{
				$msg = JText::_('SETTING_INVALID_CITY_LENGH');
			}
			else if(isset($data['state']) &&  $data['state'] != "" &&  (strlen($data['state']) < 3 || strlen($data['state'])> 50))
			{
				$msg = JText::_('SETTING_INVALID_STATE_LENGH');
			}
			if($msg != null)
			{
				$error['msg'] = $msg;
			}
			return $error;
		
	}
}
?>