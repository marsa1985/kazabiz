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
header ("content-type: text/xml");

require_once (JPATH_BASE . DS . 'includes' . DS . 'defines.php');
require_once (JPATH_BASE . DS . 'includes' . DS . 'framework.php');
require_once( JPATH_ADMINISTRATOR . DS ."components". DS ."com_enmasse". DS ."helpers". DS ."DatetimeWrapper.class.php");
	
	$language = JFactory::getLanguage();
	
	$details['title'] = 'En Masse Today Deal !';
	$details['link']  = 'http://'.$_SERVER['SERVER_NAME'];
	$details['description'] = 'En Masse Deal !';
	$details['language'] = $language->getTag();
	$details['copyright'] = '(C) 2010 Matamko.com. All Rights Reserved.';

	echo todayRss($details,getTodayDeal());
	 
	function getTodayDeal()
	{
		$mainframe = JFactory::getApplication('site');
	  	// deal
			$db = JFactory::getDBO();
			$query = "	SELECT
							* 
						FROM 
							#__enmasse_deal 
						WHERE
		              		published = '1' AND 
		              		end_at > '". DatetimeWrapper::getDatetimeOfNow() . "'
		              	ORDER BY
		              		position Asc
		              	LIMIT
		              		1
		              ";
			$db->setQuery( $query );
			$deal = $db->loadObject();
			return $deal;
	}
	
     //--------------------
	// to generate the rss content for today of deal
	function todayRss($details,$item)
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
					<title><![CDATA['.$details['title'].']]></title> 
					<link><![CDATA['.$details['link'].']]></link> 
					<description><![CDATA['.$details['description'].']]></description> 
					<language><![CDATA['.$details['language'].']]></language> 
					<copyright><![CDATA['.$details['copyright'].']]></copyright> ';
		$images = unserialize(urldecode($item->pic_dir));
		$image = str_replace("\\","/",$images[0]);
		$rss .='<item> 
			        <title><![CDATA['.$item->name.' ]]></title> 
				    <description><![CDATA['.$item->short_desc.']]></description> 
					<link><![CDATA[ http://'.$server.'/index.php?option=com_enmasse&controller=deal&task=today]]></link> 
					<image >
			  			<url><![CDATA[http://'.$server.'/'.$image.']]></url>
			  			<link><![CDATA[ http://'.$server.'/index.php?option=com_enmasse&controller=deal&task=today]]></link> 
					</image>
				</item> ';
	
	$rss .=' </channel> 
		    </rss>';
	return $rss;
	}
?>