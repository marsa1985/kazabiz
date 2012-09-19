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
	
	$details['title'] = 'En Masse Deal List !';
	$details['link']  = 'http://'.$_SERVER['SERVER_NAME'];
	$details['description'] = 'En Masse Deal List !';
	$details['language'] = $language->getTag();
	$details['copyright'] = '(C) 2010 Matamko.com. All Rights Reserved.';

	echo str_replace('\\','',deallistRss($details,getListDeal()));
	 
	function getListDeal()
	{
		$mainframe = JFactory::getApplication('site');
	  	// deal
			$db = JFactory::getDBO();
			$query = "	SELECT 
						* 
					FROM 
						#__enmasse_deal 
					WHERE
					    status NOT LIKE 'Pending' AND
		          		published = '1' AND
						start_at <='".DatetimeWrapper::getDatetimeOfNow()."' 
		          		AND end_at >= '".DatetimeWrapper::getDatetimeOfNow()."' ";
			$db->setQuery( $query );
		    $rows = $db->loadObjectList();
			return $rows;
	}
	
     //--------------------
	// to generate the rss content for list of deal
	function deallistRss($details,$item)
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
		
		$rss = '<?xml version="1.0" encoding="UTF-8"?> 
 
        <rss version="2.0">
        <channel> 
		<title><![CDATA['.$details['title'].']]></title> 
		<link><![CDATA['.$details['link'].']]></link> 
		<description><![CDATA['.$details['description'].']]></description> 
		<language><![CDATA['.$details['language'].']]></language> 
		<copyright><![CDATA['.$details['copyright'].']]></copyright> ';
		
		for ($i=0; $i< count($item);$i++)
		{
			$images = unserialize(urldecode($item[$i]->pic_dir));
			$image = str_replace("\\","/",$images[0]);
			$rss .='<item> 
				<title><![CDATA['.$item[$i]->name.']]></title> 
				<description><![CDATA['.$item[$i]->short_desc.']]></description> 
				<link><![CDATA[http://'.$server.'/index.php?option=com_enmasse&controller=deal&task=view&id='.$item[$i]->id.']]></link>';
			if($image!='')
			{
				$rss .='<image >
				  <url><![CDATA[http://'.$server.'/'.$image.']]></url>
				  <link><![CDATA[ http://'.$server.'/index.php?option=com_enmasse&controller=deal&task=view&id='.$item[$i]->id.']]></link> 
					</image>';
			}
			$rss .='</item> ';
		}
		
		$rss .='</channel> 
			                </rss>';
		return $rss;
	}
?>