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
require_once( JPATH_ADMINISTRATOR . DS ."components". DS ."com_enmasse".DS."helpers". DS ."EnmasseHelper.class.php");

$theme =  EnmasseHelper::getThemeFromSetting();//getThemeFromSetting();
JFactory::getDocument()->addScript("components/com_enmasse/theme/js/jquery/jquery-1.6.2.min.js");
JFactory::getDocument()->addScriptDeclaration('jQuery.noConflict()');

// load language pack
$language = JFactory::getLanguage();
$base_dir = JPATH_SITE.DS.'components'.DS.'com_enmasse';
$version = new JVersion;
$joomla = $version->getShortVersion();
if(substr($joomla,0,3) >= '1.6'){
    $extension = 'com_enmasse16';
}else{
    $extension = 'com_enmasse';
}
if($language->load($extension, $base_dir, $language->getTag(), true) == false)
{
	 $language->load($extension, $base_dir, 'en-GB', true);
}

?>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<div class="module_content">
	<div id="SummarizedCartModule" class="mod_SummarizedCart">
	  <div class="left_cl">
	    <div class="mod_SummarizedCart_title"><?php echo JTEXT::_('CART_TOTAL_ITEM');?></div>
	    <div class="mod_SummarizedCart_information"><?php echo $cart->getTotalItem();?></div>
		<input type="button" class="button" value="<?php echo JText::_('VIEW_CART_BUTTON');?>" onclick="window.location.href='<?php echo JRoute::_('index.php?option=com_enmasse&controller=shopping&task=checkout')?>'"></input>
	  </div>
	  <div class="right_cl">
	    <div class="mod_SummarizedCart_title"><?php echo JTEXT::_('CART_TOTAL_PRICE');?></div>
	    <div class="mod_SummarizedCart_information"><?php echo EnmasseHelper::displayCurrency($cart->getAmountToPay());?></div>
		<input type="button" class="button" value="<?php echo JText::_('CHECK_OUT_BUTTON');?>" onclick="window.location.href='<?php echo JRoute::_('index.php?option=com_enmasse&controller=shopping&task=checkout')?>'"></input>
	  </div>
	</div>
</div>