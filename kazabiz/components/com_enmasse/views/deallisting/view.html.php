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
jimport( 'joomla.application.component.view');

require_once( JPATH_ADMINISTRATOR . DS ."components". DS ."com_enmasse". DS ."helpers". DS ."EnmasseHelper.class.php");

class EnmasseViewDealListing extends JView
{
	function display($tpl = null)
	{
		$sortBy		= JRequest::getVar('sortBy', null);
		$keyword 	= JRequest::getVar('keyword', null);
		$categoryId = JRequest::getInt('categoryId', null);
		$locationId 	= JRequest::getInt('locationId', null);
		$task 	= JRequest::getVar('task', null); //When searching for deal, we will have this variable

		if($task!="display") //If user doesn't use search function, set location to the location user subscribed
		{
			$locationId = JFactory::getSession()->get('CS_SESSION_LOCATIONID');
			
		}
		else //If user is searching deal, use the location user chose
		{
			$locationId 	= JRequest::getInt('locationId', null);
		}
		
        $this->assignRef( 'sortBy', $sortBy );
		$this->assignRef( 'keyword', $keyword );
		$this->assignRef( 'categoryId', $categoryId );
		$this->assignRef( 'locationId', $locationId );
		
		
		$this->dealList = JModel::getInstance('deal','enmasseModel')->searchStartedPublishedDeal($keyword, $categoryId, $locationId, $sortBy);
	    
		$arLoc = JModel::getInstance('location','enmasseModel')->listAllPublished();
		$arCat = JModel::getInstance('category','enmasseModel')->listAllPublished();
		
		$this->assignRef( 'locationList', $arLoc);
		$this->assignRef( 'categoryList', $arCat);
			
		$this->_setPath('template',JPATH_SITE . DS ."components". DS ."com_enmasse". DS ."theme". DS .EnmasseHelper::getThemeFromSetting(). DS ."tmpl". DS);
		$this->_layout="deal_listing";
		
		parent::display($tpl);
	}

}
?>