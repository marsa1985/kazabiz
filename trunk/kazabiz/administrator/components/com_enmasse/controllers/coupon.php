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

class EnmasseControllerCoupon extends JController
{

	function display($cachable = false, $urlparams = false)
	{
		JRequest::setVar('view', 'coupon');
		JRequest::setVar('layout', 'edit');
		parent::display();
	}

	function addElement()
	{
		JRequest::setVar('view', 'coupon');
		JRequest::setVar('layout', 'edit_element');
		parent::display();
	}

	function editElement()
	{
		JRequest::setVar('view', 'coupon');
		JRequest::setVar('layout', 'edit_element');
		parent::display();
	}

	function cancelElement()
	{
		$this->setRedirect('index.php?option=com_enmasse&controller=coupon');
	}
	
	function save()
	{
		$url = JRequest::getVar( 'coupon_bg_url', '', 'post', 'text', JREQUEST_ALLOWRAW );				

		$model = JModel::getInstance('coupon','enmasseModel');
		if ($model->updateCouponBgUrl($url))
		{
            $strJSON = $_POST['jsondata'];
			$strJSON = str_replace("\\", "", $strJSON);           
            $strJSON = str_replace('px', '', $strJSON);
            $strJSON = str_replace('left', 'x', $strJSON);
            $strJSON = str_replace('top', 'y', $strJSON);
            $objData = json_decode($strJSON);
            foreach($objData as $name=>$arr)
            {
                $arrElements = array();
                $arrElements['id'] = $name;
                foreach($arr as $key=>$value)
                {
                    $arrElements[$key] = $value;
                }
                $model = JModel::getInstance('couponElement','enmasseModel');
                if(!$model->store($arrElements))
                {
                    $msg = JText::_('SAVE_ERROR_MSG') .": " . $model->getError();
                    $this->setRedirect('index.php?option=com_enmasse&controller=coupon', $msg, 'error');
                }
            }
            $msg = JText::_('SAVE_SUCCESS_MSG');
			$this->setRedirect('index.php?option=com_enmasse&controller=coupon');
		}
		else
		{
			$msg = JText::_('SAVE_ERROR_MSG') .": " . $model->getError();
			$this->setRedirect('index.php?option=com_enmasse&controller=coupon', $msg, 'error');
		}
	}

	function saveElement()
	{
		$data = JRequest::get( 'post' );
		
		$model = JModel::getInstance('couponElement','enmasseModel');

		if ($model->store($data))
		{
			$msg = JText::_('SAVE_SUCCESS_MSG');
			$this->setRedirect('index.php?option=com_enmasse&controller=' . JRequest::getVar('controller'), $msg);
		}
		else
		{
			$msg = JText::_('SAVE_ERROR_MSG') .": " . $model->getError();
			if($data['id'] == null)
				$this->setRedirect('index.php?option=com_enmasse&controller='.JRequest::getVar('controller').'&task=addElement', $msg, 'error');
			else
				$this->setRedirect('index.php?option=com_enmasse&controller='.JRequest::getVar('controller').'&task=editElement&cid[0]='. $data['id'], $msg, 'error');
		}

		$this->setRedirect('index.php?option=com_enmasse&controller=coupon', $msg);
	}

	function removeElement()
	{
		$elementId = JRequest::getVar('elementId');

		$model = JModel::getInstance('couponElement','enmasseModel');
		if ($model->delete($elementId))
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

	function cancel()
	{
		$this->setRedirect('index.php?option=com_enmasse');
	}
}
?>