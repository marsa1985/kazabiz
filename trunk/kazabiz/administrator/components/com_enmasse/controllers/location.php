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

class EnmasseControllerLocation extends JController
{

	function display($cachable = false, $urlparams = false)
	{
		JRequest::setVar('view', 'location');
		JRequest::setVar('layout', 'show');
		parent::display();
	}
	function edit()
	{
		JRequest::setVar('view', 'location');
		JRequest::setVar('layout', 'edit');
		parent::display();
	}
	function control()
	{
		$this->setRedirect('index.php?option=com_enmasse');
	}
	function add()
	{
		JRequest::setVar('view', 'location');
		JRequest::setVar('layout', 'edit');
		parent::display();
	}
	function save()
	{
		$data = JRequest::get( 'post' );

		$model = JModel::getInstance('location','enmasseModel');
		$err = $this->validateLocation($data);
		//------------------------
		//gemerate integration class
		 $integrateFileName = EnmasseHelper::getSubscriptionClassFromSetting().'.class.php';
		 $integrationClass = EnmasseHelper::getSubscriptionClassFromSetting();
		 require_once (JPATH_SITE . DS ."components". DS ."com_enmasse". DS ."helpers". DS ."subscription". DS .$integrationClass. DS.$integrateFileName);
		 $integrationObject = new $integrationClass();
		if(! empty($err))
		{
			$msg = JText::_('SAVE_ERROR_MSG') .": " . $err['msg'];
			if($data['id'] == null)
				$this->setRedirect('index.php?option=com_enmasse&controller='.JRequest::getVar('controller').'&task=add', $msg, 'error');
			else
				$this->setRedirect('index.php?option=com_enmasse&controller='.JRequest::getVar('controller').'&task=edit&cid[0]='. $data['id'], $msg, 'error');
		}	
		else if ($model->store($data))
		{
			$integrationObject->integration($row,'location');
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

	function remove()
	{
		$cids = JRequest::getVar( 'cid', array(0), 'post', 'array' );

		$model = JModel::getInstance('location','enmasseModel');
		$msg = "";
		for($count=0; $count <count($cids); $count++)
		{			
			$dealList = JModel::getInstance("dealLocation","enmasseModel")->getDealByLocationId($cids[$count]);
			if(count($dealList)!=0)
			{
				$location = $model->getById($cids[$count]);
				$msg .= $location->name.' ';
				$msg .= JText::_("LOCATION_IS_BEING_ASSIGNED_TO_A_DEAL") . "<br />";
				unset($cids[$count]);
			}
		}
		
		//------------------------
		//gemerate integration class
		 $integrateFileName = EnmasseHelper::getSubscriptionClassFromSetting().'.class.php';
		 $integrationClass = EnmasseHelper::getSubscriptionClassFromSetting();
		 require_once (JPATH_SITE . DS ."components". DS ."com_enmasse". DS ."helpers". DS ."subscription". DS .$integrationClass. DS.$integrateFileName);
		 $integrationObject = new $integrationClass();

		if($model->deleteList($cids))
		{
			// to remove location from deal
			/*remove at 18/05/2011
			 * for($i=0; $i < count($cids); $i++)
			{
				$integrationObject->integration($cids[$i],'removeLocation');
				JModel::getInstance('dealLocation','enmasseModel')->removeByLocation($cids[$i]);
			}*/
			
			// Commented on July 27, 2011
			//$msg = JText::_('DELETE_SUCCESS_MSG');
			//$this->setRedirect('index.php?option=com_enmasse&controller='.JRequest::getVar('controller'), $msg );
		}
		else
		{ 
			$msg .= JText::_('DELETE_ERROR_MSG') .": " . $model->getError();
			$this->setRedirect('index.php?option=com_enmasse&controller='.JRequest::getVar('controller'), $msg, 'error');
		}
		JFactory::getApplication()->redirect('index.php?option=com_enmasse&controller=location', $msg , 'error');
	}



	function publish()
	{
		EnmasseHelper::changePublishState(1,'enmasse_location','location','location');
	}
	function unpublish()
	{
		EnmasseHelper::changePublishState(0,'enmasse_location','location','location');
	}
	function checkDuplicatedLocation()
	{
		
		$locationName = addslashes(JRequest::getVar("locationName"));
		$locationObj = JModel::getInstance('location','enmasseModel')->getLocationByName($locationName);
		if($locationObj!=null)
	    	echo 'true';
	    else
	    	echo 'false';
		exit(0);
		
	}
	
	function updateAcyList()
	{
		global $mainframe;
        $version = new JVersion;
        $joomla = $version->getShortVersion();
        if (substr($joomla, 0, 3) >= 1.6) {
            $mainframe = JFactory::getApplication();
        }

        // define variable $cid from GET
        $cid = JRequest::getVar('cid', array(), '', 'array');
        JArrayHelper::toInteger($cid);

        // Check there is/are item that will be changed.
        //If not, show the error.
        $error = false;
        $msg;
        if (count($cid) < 1) {
           $msg = JText::_('NO_ITEM_SELECTED');
           $error = true;
        } else {
        	// Prepare sql statement, if cid more than one,
	        // it will be "cid1, cid2, cid3, ..."
	        $cids = implode(',', $cid);
	
	        $query = "SELECT * FROM #__enmasse_location where id IN ( " . $cids . " )";
	        
	        // Initialize variables
        	$db =  JFactory::getDBO();
	        
	        // Execute query
	        $db->setQuery( $query );
			$locationList = $db->loadObjectList();
			
			if ($db->getErrorNum()) {
				$msg = $db->getErrorMsg();
				$error = true;
			} else {
	
				//------------------------
				//gemerate integration class
				 $integrateFileName = EnmasseHelper::getSubscriptionClassFromSetting().'.class.php';
				 $integrationClass = EnmasseHelper::getSubscriptionClassFromSetting();
				 require_once (JPATH_SITE . DS ."components". DS ."com_enmasse". DS ."helpers". DS ."subscription". DS .$integrationClass. DS.$integrateFileName);
				 $integrationObject = new $integrationClass();
				 foreach ($locationList as $item)
				 {
				 	$item->oldname = $item->name;
				 	$integrationObject->insertEnmasseLocation($item);
				 }
		
		       $msg = JText::_( "ACY_LOCATION_UPDATED");
			}
        }
         // After all, redirect to front page
        $link = JRoute::_("index.php?option=com_enmasse&controller=".JRequest::getVar('controller'), false);
        if ($error) {
			JFactory::getApplication()->redirect($link, $msg, 'error');
        } else {
        	JFactory::getApplication()->redirect($link, $msg);
        }
	}
	function  validateLocation($data = array())
	{
		$msg = null;
		$error = array();
		if(isset($data['name']) && ( strlen($data['name']) < 8 || strlen($data['name'])> 50))
		{
			$msg = JText::_('LOCATION_INVALID_NAME_LENGH');
		}
		else if(isset($data['description']) &&  $data['description'] != "" &&   strlen($data['description'])> 100)
		{
			$msg = JText::_('LOCATION_INVALID_DESCRIPTION_LENGH');
		}
	
		if($msg != null)
		{
			$error['msg'] = $msg;
		}
		return $error;
	}
}
?>