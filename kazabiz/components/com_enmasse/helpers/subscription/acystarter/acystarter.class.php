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
class acystarter
{
	 function integration($data,$key)
	 {
	 	if($key=="location")
	 	{
			acystarter::insertEnmasseLocation($data);
	 	}
	   else if($key == "newDeal")
	 	{
	 		acystarter::insertNewLetter($data);
	 	}
	 	return true;
	 }
     function addMenu()
	 {
	 	 return JToolBarHelper::custom( 'updateAcyList', 'upload.png', 'upload.png', 'T_ACY_UPDATE_LOCATION_LIST', false,  false );
	 }
	 
    function insertNewLetter($data)
	{
		$sub = $data->name;
		$slug_name = acystarter::seoUrl($data->name);
		$type = "news";
		$body   = $data->name.'<br>';
		$imageUrlArr = unserialize(urldecode($data->pic_dir));
		$body .= '<img src=\"'.str_replace("\\","/",$imageUrlArr[0]).'\" />';
		$body .= '<br>'.$data->short_desc.'<br>'.$data->description;
		$body .='<br><table border=0><tr><td>'.$data->highlight.'</td><td>'.$data->terms.'</td></tr></table>';
		
		$db = JFactory::getDBO();
		$query = 'INSERT INTO #__acymailing_mail (`subject`,`body`,`type`,`alias`,`html`) 
		         VALUE ("' .$sub. '","' .$body. '","'. $type. '","'. $slug_name. '", 1)';
		$db->setQuery($query);
		$db->query();
	}
	
	
	function seoUrl($string)
	{
		//Unwanted:  {UPPERCASE} ; / ? : @ & = + $ , . ! ~ * ' ( )
		$string = strtolower($string);
		//Strip any unwanted characters
		$string = preg_replace("/[^a-z0-9_\s-]/", "", $string);
		//Clean multiple dashes or whitespaces
		$string = preg_replace("/[\s-]+/", " ", $string);
		//Convert whitespaces and underscore to dash
		$string = preg_replace("/[\s_]/", "_", $string);
		return $string;
	}
	function insertEnmasseLocation($data)
	{
		 //Add En Masse location as ACY list
		$new_name = "EnMasse - " . $data->name;
		$old_name = "EnMasse - " . $data->oldname;
		$db = JFactory::getDBO();
		$query = "SELECT * FROM #__acymailing_list WHERE name='".$old_name."'";		
		$db->setQuery($query);
		$db->query();
		$num_rows = $db->getNumRows();
//		print($num_rows);
		if($num_rows==0)
		{
			$query = "INSERT INTO #__acymailing_list (`name`, `description`, `ordering`, `listid`, `published`, `userid`, `alias`, `color`, `visible`, `welmailid`, `unsubmailid`, `type`, `access_sub`, `access_manage`, `languages`) VALUES ('".$new_name."', NULL, NULL, NULL, NULL, NULL, NULL, '#FF0000', '1', NULL, NULL, 'list', 'all', 'none', 'all');";
			$db->setQuery($query);
			$db->query();
		}
		elseif($num_rows==1)
		{
			$row = $db->loadAssoc();
			$query = "UPDATE #__acymailing_list SET `name` = '".$new_name."' WHERE `name` ='".$old_name."'";
			$db->setQuery($query);
			$db->query();			
		}			
	}
		 
	 function getViewData($params)
	 {
	 	$data = new JObject();
	 	
	 	$data->module = EnmasseHelper::getModuleById($params->module_id);
	 	$data->locationList = JModel::getInstance('location','enmasseModel')->listAllPublished();
	 	
	 	return $data;
	 }
	 
	 function updateSubscriptionList($location, $email)
	 {
		//Get Location name from Location ID
		$db = JFactory::getDBO();
		$query = "SELECT name FROM #__enmasse_location WHERE id='".$location."'";
		$db->setQuery($query);
		$location_name = $db->loadResult();
		
		//Get List ID from Location ID
		$db = JFactory::getDBO();
		$query = "SELECT listid FROM #__acymailing_list WHERE name='EnMasse - ".$location_name."'";
		$db->setQuery($query);
		$list_id = $db->loadResult();		
		
		//Get Subscriber ID from Subscriber mail
		$db = JFactory::getDBO();
		$query = "SELECT subid FROM #__acymailing_subscriber WHERE email='".$email."'";
		$db->setQuery($query);
		$sub_id = $db->loadResult();
		
		$query = "SELECT * FROM #__acymailing_listsub WHERE subid='".$sub_id."' AND listid='".$list_id."'";
		$db->setQuery($query);
		$db->query();
		$num_rows = $db->getNumRows();
		if($num_rows==0)
		{
			$query = "INSERT INTO #__acymailing_listsub (`listid`, `subid`, `status`) VALUES('".$list_id."', '".$sub_id."', '1')";
			$db->setQuery($query);
			$db->query();
		}	 
	 }
	 
}
?>