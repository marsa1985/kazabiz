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
define( '_JEXEC', 1 );
define('JPATH_BASE', dirname(__FILE__).'/../../../../../');
define( 'DS', DIRECTORY_SEPARATOR );


require_once (JPATH_BASE . DS . 'includes' . DS . 'defines.php');
require_once (JPATH_BASE . DS . 'includes' . DS . 'framework.php');
require_once( JPATH_ADMINISTRATOR . DS ."components". DS ."com_enmasse". DS ."helpers". DS ."DatetimeWrapper.class.php");
require_once( JPATH_ADMINISTRATOR . DS ."components". DS ."com_enmasse". DS ."helpers". DS ."DatetimeWrapper.class.php");

	
	$language = JFactory::getLanguage();
	
	$details['title'] = 'Deals in ';
	$details['link']  = 'http://'.$_SERVER['SERVER_NAME'];
	$details['description'] = 'En Masse Deal !';
	$details['language'] = $language->getTag();
	$details['copyright'] = '(C) 2010 Matamko.com. All Rights Reserved.';

	if($_GET['locationId'] && is_numeric($_GET['locationId']))
	{
		header ("content-type: text/xml");
		echo locationRss($details, getDealListByLocationId($_GET['locationId']),
			getLocationNameByLocationId($_GET['locationId']));
	}
	else
	{
		echo "Can't find any location!";
	}

	function getLocationNameByLocationId($locationId)
	{
		$mainframe = JFactory::getApplication('site');
		$db = JFactory::getDBO();
		$query = "SELECT name FROM #__enmasse_location WHERE id = " . $locationId;				
		$db->setQuery( $query );
		$row = $db->loadResult();	
		return $row;		
	}
	function getDealListByLocationId($locationId)
	{
		$mainframe = JFactory::getApplication('site');
		$db = JFactory::getDBO();
		$query = "SELECT *
				FROM `#__enmasse_deal`
				WHERE 
				status NOT LIKE 'Pending' AND
		          		published = '1' AND
						start_at <='".DatetimeWrapper::getDatetimeOfNow()."' 
		          		AND end_at >= '".DatetimeWrapper::getDatetimeOfNow()."' 
		          		AND id
				IN (				
					SELECT deal_id
					FROM `#__enmasse_deal_location`
					WHERE location_id = ".$locationId."
				) ORDER BY created_at DESC";				
				
		$db->setQuery( $query );
		$rows = $db->loadAssocList();	
		return $rows;
	}
	
	function locationRss($details,$items,$locationName)
	{
		
	    //---------------------------
		// to re define server link
		$temp_link_arr  = explode('/',$_SERVER['PHP_SELF']) ;
		
		$server = $_SERVER['SERVER_NAME'];
		for ($count=0; $count < count ($temp_link_arr)-6; $count++)
		{
			if ($temp_link_arr[$count]!= '')
			{
				$server.='/';
				$server.=$temp_link_arr[$count];
			}
		}
	    $rss = '<?xml version="1.0" encoding="UTF-8"?>'; 
	 
	    $rss .=' <rss version="2.0">
	        <channel> 
					<title><![CDATA['.$details['title']. $locationName . ']]></title> 
					<link><![CDATA['.$details['link'].']]></link> 
					<description><![CDATA['.$details['description'].']]></description> 
					<language><![CDATA['.$details['language'].']]></language> 
					<copyright><![CDATA['.$details['copyright'].']]></copyright> ';
	    foreach($items as $item):
		$images = unserialize(urldecode($item['pic_dir']));
		$image = str_replace("\\","/",$images[0]);
		$rss .='<item> 
			        <title><![CDATA['.$item['name'].' ]]></title> 
				    <description><![CDATA['.$item['short_desc'].']]></description> 
					<link><![CDATA[ http://'.$server.'/index.php?option=com_enmasse&controller=deal&task=view&id='.$item['id'].']]></link> 
					<image >
			  			<url><![CDATA[http://'.$server.'/'.$image.']]></url>
			  			<link><![CDATA[ http://'.$server.'/index.php?option=com_enmasse&controller=deal&task=view&id='.$item['id'].']]></link> 
					</image>
				</item> ';
		endforeach;
	
	$rss .=' </channel> 
		    </rss>';
	return $rss;
	}
?>		