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

jimport( 'joomla.application.component.model' );
require_once( JPATH_SITE . DS ."components". DS ."com_enmasse". DS ."helpers". DS ."CartHelper.class.php");
require_once( JPATH_ADMINISTRATOR . DS ."components". DS ."com_enmasse". DS ."helpers". DS ."EnmasseHelper.class.php"); 

class EnmasseModelSetting extends JModel
{
	function getCompanyName()
	{
		$oSetting = EnmasseHelper::getSetting();
		return $oSetting->company_name;
	}
	
	function getSetting()
	{
		return EnmasseHelper::getSetting();

	}
	
	function getCurrencyPrefix()
	{
		$oSetting = EnmasseHelper::getSetting();
		return $oSetting->currency_prefix;
		
	}
	function getCurrencyPostfix()
	{
		$oSetting = EnmasseHelper::getSetting();
		return $oSetting->currency_postfix;
		
	}
    function getCurrency()
	{
		$oSetting = EnmasseHelper::getSetting();
		return $oSetting->default_currency;
		
	}
	function getCountry()
	{
		$oSetting = EnmasseHelper::getSetting();
		return $oSetting->country;
		
	}
	function getCouponBg()
	{
		$oSetting = EnmasseHelper::getSetting();
		return $oSetting->coupon_bg_url;
		
	}
}
?>